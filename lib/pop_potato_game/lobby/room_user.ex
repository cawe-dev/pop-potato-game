defmodule PopPotatoGame.Lobby.RoomUser do
  use Ecto.Schema
  import Ecto.Changeset

  schema "room_users" do
    belongs_to :room, PopPotatoGame.Lobby.Room
    belongs_to :user, PopPotatoGame.Accounts.User

    timestamps(type: :utc_datetime)
  end

  @doc false
  def changeset(room_user, attrs) do
    room_user
    |> cast(attrs, [:room_id, :user_id])
    |> validate_required([:room_id, :user_id])
    |> unique_constraint([:room_id, :user_id], name: :room_users_room_id_user_id_index)
  end
end
