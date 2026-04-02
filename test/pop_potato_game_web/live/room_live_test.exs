defmodule PopPotatoGameWeb.RoomLiveTest do
  use PopPotatoGameWeb.ConnCase

  import Phoenix.LiveViewTest
  import PopPotatoGame.LobbyFixtures

  @create_attrs %{code: "some code", status: "some status", type: "some type", password: "some password", icon: "some icon", theme: "some theme", max_users: 42, game_mode: "some game_mode"}
  @update_attrs %{code: "some updated code", status: "some updated status", type: "some updated type", password: "some updated password", icon: "some updated icon", theme: "some updated theme", max_users: 43, game_mode: "some updated game_mode"}
  @invalid_attrs %{code: nil, status: nil, type: nil, password: nil, icon: nil, theme: nil, max_users: nil, game_mode: nil}
  defp create_room(_) do
    room = room_fixture()

    %{room: room}
  end

  describe "Index" do
    setup [:create_room]

    test "lists all rooms", %{conn: conn, room: room} do
      {:ok, _index_live, html} = live(conn, ~p"/rooms")

      assert html =~ "Listing Rooms"
      assert html =~ room.code
    end

    test "saves new room", %{conn: conn} do
      {:ok, index_live, _html} = live(conn, ~p"/rooms")

      assert {:ok, form_live, _} =
               index_live
               |> element("a", "New Room")
               |> render_click()
               |> follow_redirect(conn, ~p"/rooms/new")

      assert render(form_live) =~ "New Room"

      assert form_live
             |> form("#room-form", room: @invalid_attrs)
             |> render_change() =~ "can&#39;t be blank"

      assert {:ok, index_live, _html} =
               form_live
               |> form("#room-form", room: @create_attrs)
               |> render_submit()
               |> follow_redirect(conn, ~p"/rooms")

      html = render(index_live)
      assert html =~ "Room created successfully"
      assert html =~ "some code"
    end

    test "updates room in listing", %{conn: conn, room: room} do
      {:ok, index_live, _html} = live(conn, ~p"/rooms")

      assert {:ok, form_live, _html} =
               index_live
               |> element("#rooms-#{room.id} a", "Edit")
               |> render_click()
               |> follow_redirect(conn, ~p"/rooms/#{room}/edit")

      assert render(form_live) =~ "Edit Room"

      assert form_live
             |> form("#room-form", room: @invalid_attrs)
             |> render_change() =~ "can&#39;t be blank"

      assert {:ok, index_live, _html} =
               form_live
               |> form("#room-form", room: @update_attrs)
               |> render_submit()
               |> follow_redirect(conn, ~p"/rooms")

      html = render(index_live)
      assert html =~ "Room updated successfully"
      assert html =~ "some updated code"
    end

    test "deletes room in listing", %{conn: conn, room: room} do
      {:ok, index_live, _html} = live(conn, ~p"/rooms")

      assert index_live |> element("#rooms-#{room.id} a", "Delete") |> render_click()
      refute has_element?(index_live, "#rooms-#{room.id}")
    end
  end

  describe "Show" do
    setup [:create_room]

    test "displays room", %{conn: conn, room: room} do
      {:ok, _show_live, html} = live(conn, ~p"/rooms/#{room}")

      assert html =~ "Show Room"
      assert html =~ room.code
    end

    test "updates room and returns to show", %{conn: conn, room: room} do
      {:ok, show_live, _html} = live(conn, ~p"/rooms/#{room}")

      assert {:ok, form_live, _} =
               show_live
               |> element("a", "Edit")
               |> render_click()
               |> follow_redirect(conn, ~p"/rooms/#{room}/edit?return_to=show")

      assert render(form_live) =~ "Edit Room"

      assert form_live
             |> form("#room-form", room: @invalid_attrs)
             |> render_change() =~ "can&#39;t be blank"

      assert {:ok, show_live, _html} =
               form_live
               |> form("#room-form", room: @update_attrs)
               |> render_submit()
               |> follow_redirect(conn, ~p"/rooms/#{room}")

      html = render(show_live)
      assert html =~ "Room updated successfully"
      assert html =~ "some updated code"
    end
  end
end
