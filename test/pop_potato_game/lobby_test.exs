defmodule PopPotatoGame.LobbyTest do
  use PopPotatoGame.DataCase

  alias PopPotatoGame.Lobby

  describe "rooms" do
    alias PopPotatoGame.Lobby.Room

    import PopPotatoGame.LobbyFixtures

    @invalid_attrs %{code: nil, status: nil, type: nil, password: nil, icon: nil, theme: nil, max_users: nil, game_mode: nil}

    test "list_rooms/0 returns all rooms" do
      room = room_fixture()
      assert Lobby.list_rooms() == [room]
    end

    test "get_room!/1 returns the room with given id" do
      room = room_fixture()
      assert Lobby.get_room!(room.id) == room
    end

    test "create_room/1 with valid data creates a room" do
      valid_attrs = %{code: "some code", status: "some status", type: "some type", password: "some password", icon: "some icon", theme: "some theme", max_users: 42, game_mode: "some game_mode"}

      assert {:ok, %Room{} = room} = Lobby.create_room(valid_attrs)
      assert room.code == "some code"
      assert room.status == "some status"
      assert room.type == "some type"
      assert room.password == "some password"
      assert room.icon == "some icon"
      assert room.theme == "some theme"
      assert room.max_users == 42
      assert room.game_mode == "some game_mode"
    end

    test "create_room/1 with invalid data returns error changeset" do
      assert {:error, %Ecto.Changeset{}} = Lobby.create_room(@invalid_attrs)
    end

    test "update_room/2 with valid data updates the room" do
      room = room_fixture()
      update_attrs = %{code: "some updated code", status: "some updated status", type: "some updated type", password: "some updated password", icon: "some updated icon", theme: "some updated theme", max_users: 43, game_mode: "some updated game_mode"}

      assert {:ok, %Room{} = room} = Lobby.update_room(room, update_attrs)
      assert room.code == "some updated code"
      assert room.status == "some updated status"
      assert room.type == "some updated type"
      assert room.password == "some updated password"
      assert room.icon == "some updated icon"
      assert room.theme == "some updated theme"
      assert room.max_users == 43
      assert room.game_mode == "some updated game_mode"
    end

    test "update_room/2 with invalid data returns error changeset" do
      room = room_fixture()
      assert {:error, %Ecto.Changeset{}} = Lobby.update_room(room, @invalid_attrs)
      assert room == Lobby.get_room!(room.id)
    end

    test "delete_room/1 deletes the room" do
      room = room_fixture()
      assert {:ok, %Room{}} = Lobby.delete_room(room)
      assert_raise Ecto.NoResultsError, fn -> Lobby.get_room!(room.id) end
    end

    test "change_room/1 returns a room changeset" do
      room = room_fixture()
      assert %Ecto.Changeset{} = Lobby.change_room(room)
    end
  end
end
