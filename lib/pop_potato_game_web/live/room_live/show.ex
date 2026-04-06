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
          <.button
            :if={@current_scope.user.id == @room.user_id}
            variant="accent"
            phx-click="start_match"
          >
            <.icon name="hero-play" /> Start Game
          </.button>
        </:actions>
      </.header>

      <.table id="users" rows={@room.users}>
        <:col :let={user} label="nickname">{user.nickname}</:col>
        <:col :let={user} label="transfer_owner">
          <.button
            :if={@current_scope.user.id == @room.user_id}
            phx-click="transfer_owner"
            phx-value-new_owner_id={user.id}
          >
            Trasfer Owner
          </.button>
        </:col>
        <:col :let={user} label="kick_user">
          <.button
            :if={@current_scope.user.id == @room.user_id}
            phx-click="kick_user"
            phx-value-target_user_id={user.id}
          >
            Kick
          </.button>
        </:col>
      </.table>

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
      Lobby.subscribe_room(id)
      Lobby.subscribe_user_room(socket.assigns.current_scope, id)
    end

    {:ok,
     socket
     |> assign(:page_title, "Show Room")
     |> assign(:room, Lobby.get_room!(id))}
  end

  @impl true
  def handle_info(
        {:updated, %PopPotatoGame.Lobby.Room{id: id} = _room},
        %{assigns: %{room: %{id: id}}} = socket
      ) do
    {:noreply, assign(socket, :room, Lobby.get_room!(socket.assigns.room.id))}
  end

  def handle_info(
        {:deleted, %PopPotatoGame.Lobby.Room{id: id}},
        %{assigns: %{room: %{id: id}}} = socket
      ) do
    {:noreply,
     socket
     |> put_flash(:error, "The current room was deleted")
     |> push_navigate(to: ~p"/rooms")}
  end

  @impl true
  def handle_info(
        {:join, %PopPotatoGame.Lobby.Room{id: id} = _room},
        %{assigns: %{room: %{id: id}}} = socket
      ) do
    {:noreply,
     socket
     |> assign(:room, Lobby.get_room!(socket.assigns.room.id))}
  end

  def handle_info({type, %PopPotatoGame.Lobby.Room{}}, socket)
      when type in [:created, :updated, :deleted, :join] do
    {:noreply, socket}
  end

  @impl true
  def handle_info(
        {:kicked, %PopPotatoGame.Lobby.RoomUser{room_id: id} = _room_user},
        %{assigns: %{room: %{id: id}}} = socket
      ) do
    {:noreply,
     socket
     |> put_flash(:info, "You were kicked out")
     |> push_navigate(to: ~p"/rooms")}
  end

  @impl true
  def handle_event("start_match", _params, socket) do
    case Lobby.start_match(socket.assigns.current_scope, socket.assigns.room) do
      {:ok, _room} ->
        {:noreply,
         socket
         |> assign(:room, Lobby.get_room!(socket.assigns.room.id))
         |> put_flash(:info, "Starting match")}

      {:error, :only_owner_present} ->
        {:noreply,
         socket
         |> put_flash(:error, "Its necessary least 2 player to start the match")}
    end
  end

  def handle_event("transfer_owner", params, socket) do
    %{"new_owner_id" => new_owner_id} = params

    case Lobby.transfer_ownership(
           socket.assigns.current_scope,
           socket.assigns.room,
           String.to_integer(new_owner_id)
         ) do
      {:ok, _room} ->
        {:noreply,
         socket
         |> assign(:room, Lobby.get_room!(socket.assigns.room.id))
         |> put_flash(:info, "#{new_owner_id} is new owner")}

      {:error, :user_not_found} ->
        {:noreply,
         socket
         |> put_flash(:error, "Target user from a new owner is not found")}
    end
  end

  def handle_event("kick_user", params, socket) do
    %{"target_user_id" => target_user_id} = params

    case Lobby.kick_user(
           socket.assigns.current_scope,
           socket.assigns.room,
           String.to_integer(target_user_id)
         ) do
      {:ok, _room_user} ->
        {:noreply,
         socket
         |> assign(:room, Lobby.get_room!(socket.assigns.room.id))
         |> put_flash(:info, "#{target_user_id} is kicked")}

      {:error, :user_not_found} ->
        {:noreply,
         socket
         |> put_flash(:error, "Target user is not found")}
    end
  end
end
