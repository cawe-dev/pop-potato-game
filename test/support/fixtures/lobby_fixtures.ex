defmodule PopPotatoGame.LobbyFixtures do
  @moduledoc """
  This module defines test helpers for creating
  entities via the `PopPotatoGame.Lobby` context.
  """

  @doc """
  Generate a room.
  """
  def room_fixture(attrs \\ %{}) do
    {:ok, room} =
      attrs
      |> Enum.into(%{
        code: "some code",
        game_mode: "some game_mode",
        icon: "some icon",
        max_users: 42,
        password: "some password",
        status: "some status",
        theme: "some theme",
        type: "some type"
      })
      |> PopPotatoGame.Lobby.create_room()

    room
  end
end
