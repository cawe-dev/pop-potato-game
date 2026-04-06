defmodule PopPotatoGameWeb.RoomLive.Show do
  use PopPotatoGameWeb, :live_view
  import PopPotatoGameWeb.LobbyComponents

  alias PopPotatoGame.Lobby

  @impl true
  def render(assigns) do
    ~H"""
    <Layouts.app flash={@flash} current_scope={@current_scope} page_title={@page_title}>
      <.room_header
        room={@room}
        back_path={~p"/rooms"}
        edit_path={
          if @current_scope.user.id == @room.user_id,
            do: ~p"/rooms/#{@room}/edit?return_to=show",
            else: nil
        }
      />

      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6 pb-32">
        <.user_card
          :for={{user, index} <- Enum.with_index(@room.users)}
          user={user}
          slot_index={index}
          is_card_owner={user.id == @room.user_id}
          current_user_is_host={@current_scope.user.id == @room.user_id}
        />

        <.empty_slot :for={_ <- Enum.drop(1..@room.max_users, length(@room.users))} />
      </div>

      <.control_panel
        room={@room}
        current_scope={@current_scope}
        invite_link={url(~p"/rooms/#{@room}")}
      />
    </Layouts.app>
    """
  end

  @impl true
  def mount(%{"id" => id}, _session, socket) do
    if connected?(socket) do
      Lobby.subscribe_room(id)
      Lobby.subscribe_user_room(socket.assigns.current_scope, id)
    end

    room = Lobby.get_room!(id)

    {:ok,
     socket
     |> assign(:page_title, "Lobby: #{room.theme}")
     |> assign(room: room)}
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

  @impl true
  def handle_info(
        {:new_owner, %PopPotatoGame.Lobby.Room{id: id} = room},
        %{assigns: %{room: %{id: id}}} = socket
      ) do
    new_owner =
      Enum.find(room.users, fn user -> user.id == room.user_id end)

    {:noreply,
     socket
     |> assign(room: room)
     |> put_flash(:info, "#{new_owner.nickname} is new owner")}
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
        {:noreply, socket}

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
        kicked_user =
          Enum.find(socket.assigns.room.users, fn user ->
            user.id == String.to_integer(target_user_id)
          end)

        {:noreply,
         socket
         |> assign(:room, Lobby.get_room!(socket.assigns.room.id))
         |> put_flash(:info, "#{kicked_user.nickname} was kicked")}

      {:error, :user_not_found} ->
        {:noreply,
         socket
         |> put_flash(:error, "Target user is not found")}
    end
  end
end
