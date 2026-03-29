defmodule PopPotatoGame.Repo do
  use Ecto.Repo,
    otp_app: :pop_potato_game,
    adapter: Ecto.Adapters.Postgres
end
