defmodule PopPotatoGame.Repo.Migrations.CreateRoomUsers do
  use Ecto.Migration

  def change do
    create table(:room_users) do
      add :room_id, references(:rooms, on_delete: :delete_all)
      add :user_id, references(:users, on_delete: :delete_all)

      timestamps(type: :utc_datetime)
    end

    create index(:room_users, [:room_id])
    create index(:room_users, [:user_id])
    create unique_index(:room_users, [:room_id, :user_id])
  end
end
