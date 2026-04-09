defmodule PopPotatoGame.Repo.Migrations.CreateThemes do
  use Ecto.Migration

  def change do
    create table(:themes) do
      add :name, :string, null: false
      add :icon, :string

      timestamps(type: :utc_datetime)
    end

    create unique_index(:themes, [:name])
  end
end
