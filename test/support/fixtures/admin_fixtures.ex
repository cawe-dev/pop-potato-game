defmodule PopPotatoGame.AdminFixtures do
  @moduledoc """
  This module defines test helpers for creating
  entities via the `PopPotatoGame.Admin` context.
  """

  @doc """
  Generate a theme.
  """
  def theme_fixture(attrs \\ %{}) do
    {:ok, theme} =
      attrs
      |> Enum.into(%{
        icon: "some icon",
        name: "some name"
      })
      |> PopPotatoGame.Admin.create_theme()

    theme
  end
end
