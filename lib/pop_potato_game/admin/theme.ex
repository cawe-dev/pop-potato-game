defmodule PopPotatoGame.Admin.Theme do
  use Ecto.Schema
  import Ecto.Changeset

  schema "themes" do
    field :name, :string
    field :icon, :string

    timestamps(type: :utc_datetime)
  end

  @doc false
  def changeset(theme, attrs) do
    theme
    |> cast(attrs, [:name, :icon])
    |> validate_required([:name, :icon])
  end
end
