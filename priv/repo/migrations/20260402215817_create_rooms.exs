defmodule PopPotatoGame.Repo.Migrations.CreateRooms do
  use Ecto.Migration

  def change do
    create table(:rooms) do
      add :code, :string
      add :icon, :string
      add :password, :string, null: true
      add :theme, :string
      add :type, :string, default: "public", null: false
      add :max_users, :integer, default: 1, null: false
      add :game_mode, :string, default: "default", null: false
      add :status, :string, default: "waiting", null: false

      timestamps(type: :utc_datetime)
    end

    create unique_index(:rooms, [:code])
  end
end
