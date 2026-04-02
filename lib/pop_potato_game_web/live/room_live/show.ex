defmodule PopPotatoGameWeb.RoomLive.Show do
  use PopPotatoGameWeb, :live_view

  alias PopPotatoGame.Lobby

  @impl true
  def render(assigns) do
    ~H"""
    <Layouts.app flash={@flash}>
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
    {:ok,
     socket
     |> assign(:page_title, "Show Room")
     |> assign(:room, Lobby.get_room!(id))}
  end
end
