defmodule PopPotatoGameWeb.RoomLive.Index do
  use PopPotatoGameWeb, :live_view
  import PopPotatoGameWeb.LobbyComponents

  alias PopPotatoGame.Lobby

  @impl true
  def render(assigns) do
    ~H"""
    <Layouts.app flash={@flash} current_scope={@current_scope} page_title={@page_title}>
      <.header>
        <p>Listing Rooms</p>
        <:actions>
          <.create_room_ticket navigate={~p"/rooms/new"} />
        </:actions>
      </.header>

      <div
        id="rooms-stream-container"
        phx-update="stream"
        class="flex flex-col sm:flex-row justify-center sm:justify-start gap-4 mt-6"
      >
        <.ticket_room
          :for={{dom_id, room} <- @streams.rooms}
          id={dom_id}
          room_id={room.id}
          icon={"hero-#{room.icon}"}
          theme={room.theme}
          users={length(room.users)}
          max_users={room.max_users}
          type={room.type}
        />
      </div>
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
     |> stream(:rooms, list_rooms_with_index())}
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
    {:noreply, stream(socket, :rooms, list_rooms_with_index(), reset: true)}
  end

  defp list_rooms() do
    Lobby.list_rooms()
  end

  defp list_rooms_with_index() do
    list_rooms()
    |> Enum.with_index()
    |> Enum.map(fn {room, index} ->
      Map.put(room, :index, index + 1)
    end)
  end
end
