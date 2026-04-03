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

  def list_rooms(filters \\ %{})

  @doc """
  Returns the list of rooms by scope.

  ## Examples

      iex> list_rooms(scope)
      [%Room{}, ...]

  """
  def list_rooms(%Scope{} = scope) do
    Repo.all_by(Room, user_id: scope.user.id)
  end

  @doc """
  Returns the list of rooms.

  ## Examples

      iex> list_rooms()
      [%Room{}, ...]

  """
  def list_rooms(filters) when is_map(filters) do
    Room
    |> base_rooms_query()
    |> apply_room_filters(filters)
    |> Repo.all()
  end

  defp base_rooms_query(query) do
    from r in query, where: r.status != :finished
  end

  defp apply_room_filters(query, filters) do
    Enum.reduce(filters, query, fn {key, value}, current_query ->
      filter_room(current_query, key, value)
    end)
  end

  defp filter_room(query, "type", type) when is_binary(type) do
    from r in query, where: r.type == ^type
  end

  defp filter_room(query, "code", code) when is_binary(code) do
    from r in query, where: r.code == ^code
  end

  defp filter_room(query, "theme", theme) when is_list(theme) do
    from r in query, where: r.theme in ^theme
  end

  defp filter_room(query, "game_mode", game_mode) when is_list(game_mode) do
    from r in query, where: r.game_mode in ^game_mode
  end

  defp filter_room(query, _unknown_key, _value), do: query

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
    Room
    |> Repo.get_by!(id: id, user_id: scope.user.id)
    |> Repo.preload([:user, :users])
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
  def get_room!(id) do
    Room
    |> Repo.get!(id)
    |> Repo.preload([:user, :users])
  end

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

    if room.status != :waiting do
      {:error, :room_already_started}
    else
      with {:ok, room = %Room{}} <-
             room
             |> Room.changeset(attrs)
             |> Repo.update() do
        broadcast_room(scope, {:updated, room})
        {:ok, room}
      end
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

  def join_room(%Scope{} = scope, %Room{} = room, provided_password \\ nil) do
    if room.type == :private && room.password != provided_password do
      {:error, :invalid_password}
    else
      users_count = Repo.aggregate(from(ru in RoomUser, where: ru.room_id == ^room.id), :count)

      if users_count >= room.max_users do
        {:error, :room_full}
      else
        attrs = %{
          room_id: room.id,
          user_id: scope.user.id
        }

        Repo.insert(RoomUser.changeset(%RoomUser{}, attrs))
      end
    end
  end

  def leave_room(%Scope{} = scope, %Room{} = room) do
    room_user = Repo.get_by(RoomUser, room_id: room.id, user_id: scope.user.id)
    if room_user, do: Repo.delete(room_user)

    remaining_users = Repo.all(from ru in RoomUser, where: ru.room_id == ^room.id)

    cond do
      Enum.empty?(remaining_users) ->
        Repo.delete(room)

      room.user_id == scope.user.id ->
        new_owner = List.first(remaining_users)

        room
        |> Ecto.Changeset.change(user_id: new_owner.user_id)
        |> Repo.update()

      true ->
        {:ok, :left}
    end
  end
end
