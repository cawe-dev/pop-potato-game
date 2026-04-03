defmodule PopPotatoGame.Lobby.Room do
  use Ecto.Schema
  import Ecto.Changeset

  schema "rooms" do
    field :code, :string
    field :icon, :string
    field :password, :string
    field :theme, :string
    field :type, Ecto.Enum, values: [:public, :private], default: :public
    field :max_users, :integer
    field :game_mode, :string
    field :status, :string, default: "waiting"

    belongs_to :user, PopPotatoGame.Accounts.User

    timestamps(type: :utc_datetime)
  end

  def type_label(:public), do: "Public"
  def type_label(:private), do: "Private"
  def type_label(_), do: ""

  @doc false
  def changeset(room, attrs) do
    room
    |> cast(attrs, [:theme, :type, :max_users, :game_mode, :password])
    |> validate_required([
      :theme,
      :type,
      :max_users,
      :game_mode,
      :status,
      :user_id
    ])
    |> handle_password_by_type()
  end

  @doc false
  def changeset(room, attrs, user_scope) do
    room
    |> Ecto.Changeset.change()
    |> put_change(:user_id, user_scope.user.id)
    |> changeset(attrs)
  end

  @doc false
  def save_changeset(room, attrs, user_scope) do
    room
    |> Ecto.Changeset.change()
    |> put_change(:user_id, user_scope.user.id)
    |> changeset(attrs)
    |> put_generate_code()
    |> put_icon()
    |> validate_required([:code, :icon])
  end

  defp put_generate_code(changeset) do
    changeset
    |> put_change(:code, generate_code())
  end

  defp generate_code() do
    :crypto.strong_rand_bytes(2)
    |> Base.encode16()
  end

  defp handle_password_by_type(changeset) do
    if get_field(changeset, :type) != :private do
      changeset
      |> delete_change(:password)
      |> put_change(:password, nil)
    else
      validate_required(changeset, [:password])
    end
  end

  defp put_icon(changeset) do
    icon = "academic-cap"

    changeset
    |> put_change(:icon, icon)
  end
end
