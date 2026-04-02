defmodule PopPotatoGameWeb.RoomLive.Index do
  use PopPotatoGameWeb, :live_view

  alias PopPotatoGame.Lobby

  @impl true
  def render(assigns) do
    ~H"""
    <Layouts.app flash={@flash}>
      <.header>
        Listing Rooms
        <:actions>
          <.button variant="primary" navigate={~p"/rooms/new"}>
            <.icon name="hero-plus" /> New Room
          </.button>
        </:actions>
      </.header>

      <.table
        id="rooms"
        rows={@streams.rooms}
        row_click={fn {_id, room} -> JS.navigate(~p"/rooms/#{room}") end}
      >
        <:col :let={{_id, room}} label="Code">{room.code}</:col>
        <:col :let={{_id, room}} label="Icon">{room.icon}</:col>
        <:col :let={{_id, room}} label="Password">{room.password}</:col>
        <:col :let={{_id, room}} label="Theme">{room.theme}</:col>
        <:col :let={{_id, room}} label="Type">{room.type}</:col>
        <:col :let={{_id, room}} label="Max users">{room.max_users}</:col>
        <:col :let={{_id, room}} label="Game mode">{room.game_mode}</:col>
        <:col :let={{_id, room}} label="Status">{room.status}</:col>
        <:action :let={{_id, room}}>
          <div class="sr-only">
            <.link navigate={~p"/rooms/#{room}"}>Show</.link>
          </div>
          <.link navigate={~p"/rooms/#{room}/edit"}>Edit</.link>
        </:action>
        <:action :let={{id, room}}>
          <.link
            phx-click={JS.push("delete", value: %{id: room.id}) |> hide("##{id}")}
            data-confirm="Are you sure?"
          >
            Delete
          </.link>
        </:action>
      </.table>
    </Layouts.app>
    """
  end

  @impl true
  def mount(_params, _session, socket) do
    {:ok,
     socket
     |> assign(:page_title, "Listing Rooms")
     |> stream(:rooms, list_rooms())}
  end

  @impl true
  def handle_event("delete", %{"id" => id}, socket) do
    room = Lobby.get_room!(id)
    {:ok, _} = Lobby.delete_room(room)

    {:noreply, stream_delete(socket, :rooms, room)}
  end

  defp list_rooms() do
    Lobby.list_rooms()
  end
end
