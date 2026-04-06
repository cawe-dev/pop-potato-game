defmodule PopPotatoGameWeb.RoomLive.Index do
  use PopPotatoGameWeb, :live_view

  alias PopPotatoGame.Lobby

  @impl true
  def render(assigns) do
    ~H"""
    <Layouts.app flash={@flash} current_scope={@current_scope} page_title={@page_title}>
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
        row_click={fn {_id, room} -> JS.push("join_room", value: %{room_id: room.id}) end}
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
            phx-click={JS.push("delete", value: %{id: room.id})}
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
    if connected?(socket) do
      Lobby.subscribe_rooms()
    end

    {:ok,
     socket
     |> assign(:page_title, "Listing Rooms")
     |> stream(:rooms, list_rooms())}
  end

  @impl true
  def handle_event("delete", %{"id" => id}, socket) do
    room = Lobby.get_room!(id)

    case Lobby.delete_room(socket.assigns.current_scope, room) do
      {:ok, _room} ->
        {:noreply, stream_delete(socket, :rooms, room)}

      {:error, :unathorized} ->
        {:noreply,
         socket
         |> put_flash(:error, "You is Unathorized to complete this action")}

      _ ->
        {:noreply,
         socket
         |> put_flash(:error, "Error to try delete a room")}
    end
  end

  def handle_event("join_room", params, socket) do
    %{"room_id" => room_id} = params
    room = Lobby.get_room!(room_id)

    case Lobby.join_room(socket.assigns.current_scope, room) do
      :ok ->
        {:noreply,
         socket
         |> put_flash(:info, "Entring in room")
         |> push_navigate(to: ~p"/rooms/#{room.id}")}

      {:error, :room_full} ->
        {:noreply,
         socket
         |> put_flash(:error, "This room are full")}

      {:error, :error_on_join} ->
        {:noreply,
         socket
         |> put_flash(:error, "Error to try entring to room")}

      {:error, _term} ->
        {:noreply,
         socket
         |> put_flash(:error, "Error to try entring to room")}
    end
  end

  @impl true
  def handle_info({type, %PopPotatoGame.Lobby.Room{}}, socket)
      when type in [:created, :updated, :deleted] do
    {:noreply, stream(socket, :rooms, list_rooms(), reset: true)}
  end

  defp list_rooms() do
    Lobby.list_rooms()
  end
end
