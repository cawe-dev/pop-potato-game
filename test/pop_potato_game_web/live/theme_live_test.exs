defmodule PopPotatoGameWeb.ThemeLiveTest do
  use PopPotatoGameWeb.ConnCase

  import Phoenix.LiveViewTest
  import PopPotatoGame.AdminFixtures

  @create_attrs %{name: "some name", icon: "some icon"}
  @update_attrs %{name: "some updated name", icon: "some updated icon"}
  @invalid_attrs %{name: nil, icon: nil}
  defp create_theme(_) do
    theme = theme_fixture()

    %{theme: theme}
  end

  describe "Index" do
    setup [:create_theme]

    test "lists all themes", %{conn: conn, theme: theme} do
      {:ok, _index_live, html} = live(conn, ~p"/themes")

      assert html =~ "Listing Themes"
      assert html =~ theme.name
    end

    test "saves new theme", %{conn: conn} do
      {:ok, index_live, _html} = live(conn, ~p"/themes")

      assert {:ok, form_live, _} =
               index_live
               |> element("a", "New Theme")
               |> render_click()
               |> follow_redirect(conn, ~p"/themes/new")

      assert render(form_live) =~ "New Theme"

      assert form_live
             |> form("#theme-form", theme: @invalid_attrs)
             |> render_change() =~ "can&#39;t be blank"

      assert {:ok, index_live, _html} =
               form_live
               |> form("#theme-form", theme: @create_attrs)
               |> render_submit()
               |> follow_redirect(conn, ~p"/themes")

      html = render(index_live)
      assert html =~ "Theme created successfully"
      assert html =~ "some name"
    end

    test "updates theme in listing", %{conn: conn, theme: theme} do
      {:ok, index_live, _html} = live(conn, ~p"/themes")

      assert {:ok, form_live, _html} =
               index_live
               |> element("#themes-#{theme.id} a", "Edit")
               |> render_click()
               |> follow_redirect(conn, ~p"/themes/#{theme}/edit")

      assert render(form_live) =~ "Edit Theme"

      assert form_live
             |> form("#theme-form", theme: @invalid_attrs)
             |> render_change() =~ "can&#39;t be blank"

      assert {:ok, index_live, _html} =
               form_live
               |> form("#theme-form", theme: @update_attrs)
               |> render_submit()
               |> follow_redirect(conn, ~p"/themes")

      html = render(index_live)
      assert html =~ "Theme updated successfully"
      assert html =~ "some updated name"
    end

    test "deletes theme in listing", %{conn: conn, theme: theme} do
      {:ok, index_live, _html} = live(conn, ~p"/themes")

      assert index_live |> element("#themes-#{theme.id} a", "Delete") |> render_click()
      refute has_element?(index_live, "#themes-#{theme.id}")
    end
  end

  describe "Show" do
    setup [:create_theme]

    test "displays theme", %{conn: conn, theme: theme} do
      {:ok, _show_live, html} = live(conn, ~p"/themes/#{theme}")

      assert html =~ "Show Theme"
      assert html =~ theme.name
    end

    test "updates theme and returns to show", %{conn: conn, theme: theme} do
      {:ok, show_live, _html} = live(conn, ~p"/themes/#{theme}")

      assert {:ok, form_live, _} =
               show_live
               |> element("a", "Edit")
               |> render_click()
               |> follow_redirect(conn, ~p"/themes/#{theme}/edit?return_to=show")

      assert render(form_live) =~ "Edit Theme"

      assert form_live
             |> form("#theme-form", theme: @invalid_attrs)
             |> render_change() =~ "can&#39;t be blank"

      assert {:ok, show_live, _html} =
               form_live
               |> form("#theme-form", theme: @update_attrs)
               |> render_submit()
               |> follow_redirect(conn, ~p"/themes/#{theme}")

      html = render(show_live)
      assert html =~ "Theme updated successfully"
      assert html =~ "some updated name"
    end
  end
end
