defmodule PopPotatoGame.Lobby.Room do
  use Ecto.Schema
  import Ecto.Changeset

  schema "rooms" do
    field :code, :string
    field :icon, :string
    field :password, :string
    field :theme, :string
    field :type, :string
    field :max_users, :integer
    field :game_mode, :string
    field :status, :string

    timestamps(type: :utc_datetime)
  end

  @doc false
  def changeset(room, attrs) do
    room
    |> cast(attrs, [:code, :icon, :password, :theme, :type, :max_users, :game_mode, :status])
    |> validate_required([:code, :icon, :password, :theme, :type, :max_users, :game_mode, :status])
  end
end
