defmodule PopPotatoGameWeb.RoomLive.Show do
  use PopPotatoGameWeb, :live_view

  alias PopPotatoGame.Lobby

  @impl true
  def render(assigns) do
    ~H"""
    <Layouts.app flash={@flash} current_scope={@current_scope}>
      <.header>
        Room {@room.id}
        <:subtitle>This is a room record from your database.</:subtitle>
        <:actions>
          <.button navigate={~p"/rooms"}>
            <.icon name="hero-arrow-left" />
          </.button>
          <.button variant="primary" navigate={~p"/rooms/#{@room}/edit?return_to=show"}>
            <.icon name="hero-pencil-square" /> Edit room
          </.button>
        </:actions>
      </.header>

      <.list>
        <:item title="Code">{@room.code}</:item>
        <:item title="Icon">{@room.icon}</:item>
        <:item title="Password">{@room.password}</:item>
        <:item title="Theme">{@room.theme}</:item>
        <:item title="Type">{@room.type}</:item>
        <:item title="Max users">{@room.max_users}</:item>
        <:item title="Game mode">{@room.game_mode}</:item>
        <:item title="Status">{@room.status}</:item>
      </.list>
    </Layouts.app>
    """
  end

  @impl true
  def mount(%{"id" => id}, _session, socket) do
    if connected?(socket) do
      Lobby.subscribe_rooms(socket.assigns.current_scope)
    end

    {:ok,
     socket
     |> assign(:page_title, "Show Room")
     |> assign(:room, Lobby.get_room!(socket.assigns.current_scope, id))}
  end

  @impl true
  def handle_info(
        {:updated, %PopPotatoGame.Lobby.Room{id: id} = room},
        %{assigns: %{room: %{id: id}}} = socket
      ) do
    {:noreply, assign(socket, :room, room)}
  end

  def handle_info(
        {:deleted, %PopPotatoGame.Lobby.Room{id: id}},
        %{assigns: %{room: %{id: id}}} = socket
      ) do
    {:noreply,
     socket
     |> put_flash(:error, "The current room was deleted.")
     |> push_navigate(to: ~p"/rooms")}
  end

  def handle_info({type, %PopPotatoGame.Lobby.Room{}}, socket)
      when type in [:created, :updated, :deleted] do
    {:noreply, socket}
  end
end
