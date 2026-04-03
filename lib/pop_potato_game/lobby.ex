defmodule PopPotatoGame.Lobby do
  @moduledoc """
  The Lobby context.
  """

  import Ecto.Query, warn: false
  alias Ecto.Multi
  alias PopPotatoGame.Repo
  alias PopPotatoGame.Lobby.Room
  alias PopPotatoGame.Lobby.RoomUser
  alias PopPotatoGame.Accounts.Scope

  @doc """
  Returns the list of rooms.

  ## Examples

      iex> list_rooms()
      [%Room{}, ...]

  """
  def list_rooms do
    Repo.all(Room)
  end

  @doc """
  Gets a single room.

  Raises `Ecto.NoResultsError` if the Room does not exist.

  ## Examples

      iex> get_room!(123)
      %Room{}

      iex> get_room!(456)
      ** (Ecto.NoResultsError)

  """
  def get_room!(id), do: Repo.get!(Room, id)

  @doc """
  Subscribes to scoped notifications about any room changes.

  The broadcasted messages match the pattern:

    * {:created, %Room{}}
    * {:updated, %Room{}}
    * {:deleted, %Room{}}

  """
  def subscribe_rooms(%Scope{} = scope) do
    key = scope.user.id

    Phoenix.PubSub.subscribe(PopPotatoGame.PubSub, "user:#{key}:rooms")
  end

  defp broadcast_room(%Scope{} = scope, message) do
    key = scope.user.id

    Phoenix.PubSub.broadcast(PopPotatoGame.PubSub, "user:#{key}:rooms", message)
  end

  @doc """
  Returns the list of rooms.

  ## Examples

      iex> list_rooms(scope)
      [%Room{}, ...]

  """
  def list_rooms(%Scope{} = scope) do
    Repo.all_by(Room, user_id: scope.user.id)
  end

  @doc """
  Gets a single room.

  Raises `Ecto.NoResultsError` if the Room does not exist.

  ## Examples

      iex> get_room!(scope, 123)
      %Room{}

      iex> get_room!(scope, 456)
      ** (Ecto.NoResultsError)

  """
  def get_room!(%Scope{} = scope, id) do
    Repo.get_by!(Room, id: id, user_id: scope.user.id)
  end

  @doc """
  Creates a room.

  ## Examples

      iex> create_room(scope, %{field: value})
      {:ok, %Room{}}

      iex> create_room(scope, %{field: bad_value})
      {:error, %Ecto.Changeset{}}

  """
  def create_room(%Scope{} = scope, attrs) do
    room_changeset = Room.save_changeset(%Room{}, attrs, scope)

    Multi.new()
    |> Multi.insert(:room, room_changeset)
    |> Multi.run(:check_room, fn
      _repo, %{room: room} -> {:ok, room}
      _repo, %{room: {:error, _}} -> {:error, {:falied_create_room, :falied_create_room}}
    end)
    |> Multi.insert(:room_user, fn %{room: room} ->
      RoomUser.changeset(%RoomUser{}, %{
        room_id: room.id,
        user_id: scope.user.id
      })
    end)
    |> Multi.run(:check_join_user, fn
      _repo, %{room_user: room_user} ->
        {:ok, room_user}

      _repo, %{room_user: {:error, _}} ->
        {:error, {:falied_create_room, :falied_joined_user_to_room}}
    end)
    |> Repo.transact()
    |> case do
      {:ok, result} ->
        broadcast_room(scope, {:created, result.room})
        {:ok, result.room}

      {:error, step, reason, _changes} ->
        {:error, "Error in step #{step}", detail: reason}
    end
  end

  @doc """
  Updates a room.

  ## Examples

      iex> update_room(scope, room, %{field: new_value})
      {:ok, %Room{}}

      iex> update_room(scope, room, %{field: bad_value})
      {:error, %Ecto.Changeset{}}

  """
  def update_room(%Scope{} = scope, %Room{} = room, attrs) do
    true = room.user_id == scope.user.id

    with {:ok, room = %Room{}} <-
           room
           |> Room.changeset(attrs, scope)
           |> Repo.update() do
      broadcast_room(scope, {:updated, room})
      {:ok, room}
    end
  end

  @doc """
  Deletes a room.

  ## Examples

      iex> delete_room(scope, room)
      {:ok, %Room{}}

      iex> delete_room(scope, room)
      {:error, %Ecto.Changeset{}}

  """
  def delete_room(%Scope{} = scope, %Room{} = room) do
    true = room.user_id == scope.user.id

    with {:ok, room = %Room{}} <-
           Repo.delete(room) do
      broadcast_room(scope, {:deleted, room})
      {:ok, room}
    end
  end

  @doc """
  Returns an `%Ecto.Changeset{}` for tracking room changes.

  ## Examples

      iex> change_room(scope, room)
      %Ecto.Changeset{data: %Room{}}

  """
  def change_room(%Scope{} = scope, %Room{} = room) do
    change_room(scope, room, %{})
  end

  def change_room(%Scope{} = scope, %Room{} = room, attrs) do
    true = room.user_id == scope.user.id

    Room.changeset(room, attrs, scope)
  end
end
