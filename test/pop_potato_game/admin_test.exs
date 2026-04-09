defmodule PopPotatoGame.AdminTest do
  use PopPotatoGame.DataCase

  alias PopPotatoGame.Admin

  describe "themes" do
    alias PopPotatoGame.Admin.Theme

    import PopPotatoGame.AdminFixtures

    @invalid_attrs %{name: nil, icon: nil}

    test "list_themes/0 returns all themes" do
      theme = theme_fixture()
      assert Admin.list_themes() == [theme]
    end

    test "get_theme!/1 returns the theme with given id" do
      theme = theme_fixture()
      assert Admin.get_theme!(theme.id) == theme
    end

    test "create_theme/1 with valid data creates a theme" do
      valid_attrs = %{name: "some name", icon: "some icon"}

      assert {:ok, %Theme{} = theme} = Admin.create_theme(valid_attrs)
      assert theme.name == "some name"
      assert theme.icon == "some icon"
    end

    test "create_theme/1 with invalid data returns error changeset" do
      assert {:error, %Ecto.Changeset{}} = Admin.create_theme(@invalid_attrs)
    end

    test "update_theme/2 with valid data updates the theme" do
      theme = theme_fixture()
      update_attrs = %{name: "some updated name", icon: "some updated icon"}

      assert {:ok, %Theme{} = theme} = Admin.update_theme(theme, update_attrs)
      assert theme.name == "some updated name"
      assert theme.icon == "some updated icon"
    end

    test "update_theme/2 with invalid data returns error changeset" do
      theme = theme_fixture()
      assert {:error, %Ecto.Changeset{}} = Admin.update_theme(theme, @invalid_attrs)
      assert theme == Admin.get_theme!(theme.id)
    end

    test "delete_theme/1 deletes the theme" do
      theme = theme_fixture()
      assert {:ok, %Theme{}} = Admin.delete_theme(theme)
      assert_raise Ecto.NoResultsError, fn -> Admin.get_theme!(theme.id) end
    end

    test "change_theme/1 returns a theme changeset" do
      theme = theme_fixture()
      assert %Ecto.Changeset{} = Admin.change_theme(theme)
    end
  end
end
