defmodule PopPotatoGameWeb.LobbyComponents do
  use Phoenix.Component
  use Gettext, backend: PopPotatoGameWeb.Gettext

  alias PopPotatoGameWeb.CoreComponents
  alias Phoenix.LiveView.JS

  attr :navigate, :string, required: true

  def create_room_ticket(assigns) do
    ~H"""
    <div class="ticket-wrapper min-w-xs">
      <.link
        navigate={@navigate}
        class="ticket-shape bg-primary text-card-foreground p-3 flex items-center"
      >
        <div class="flex items-center gap-4 pl-4 sm:pl-6">
          <CoreComponents.icon name="hero-plus" class="w-10 h-10 text-accent-foreground" />
          <span class="text-white font-bold uppercase text-lg leading-tight">New Room</span>
        </div>
      </.link>
    </div>
    """
  end

  attr :id, :string, required: true
  attr :room_id, :any, required: true
  attr :icon, :string, required: true
  attr :theme, :string, required: true
  attr :users, :integer, required: true
  attr :max_users, :integer, required: true
  attr :type, :string, required: true

  def ticket_room(assigns) do
    ~H"""
    <div id={@id} class="ticket-wrapper cursor-pointer min-w-xs">
      <.link
        phx-click={JS.push("join_room", value: %{room_id: @room_id})}
        class="ticket-shape bg-base-100 text-base-content p-3 flex items-center justify-between"
      >
        <div class="flex items-center gap-4 pl-4 sm:pl-6">
          <CoreComponents.icon name={@icon} class="w-10 h-10 text-primary" />
          <span class="font-black uppercase text-lg leading-tight">{@theme}</span>
        </div>
        <div class="flex flex-col items-end pr-4 sm:pr-6">
          <span class="font-bold text-xl">{@users}/{@max_users}</span>
          <span
            :if={@type}
            class="font-bold text-xs uppercase text-neutral tracking-widest mt-1"
          >
            {@type}
          </span>
        </div>
      </.link>
    </div>
    """
  end

  attr :room, :map, required: true
  attr :back_path, :string, required: true
  attr :edit_path, :string, default: nil

  def room_header(assigns) do
    ~H"""
    <section
      name="room-header"
      class="navbar bg-base-200 surface-elevated mb-6 px-4 min-h-16 flex-wrap md:flex-nowrap justify-between gap-4"
    >
      <section name="navbar-start" class="navbar-start w-full md:w-auto gap-4">
        <CoreComponents.button
          navigate={@back_path}
          class="btn-square bg-base-content text-base-100 surface-elevated-sm surface-elevated-interactive"
        >
          <CoreComponents.icon name="hero-arrow-left" class="size-6" />
        </CoreComponents.button>

        <h1 class="text-xl sm:text-2xl font-black pixel-text text-base-content uppercase truncate">
          {@room.theme}
        </h1>
      </section>

      <section name="navbar-end" class="navbar-end w-full md:w-auto gap-2">
        <div class="badge badge-lg h-10 rounded-none bg-base-100 surface-elevated-sm font-bold pixel-text text-xs sm:text-sm border-none">
          CODE: {@room.code}
          <CoreComponents.icon :if={@room.password} name="hero-lock-closed" class="size-4 ml-1" />
        </div>

        <div class="badge badge-lg h-10 rounded-none bg-base-content text-base-100 surface-elevated-sm font-bold pixel-text text-xs sm:text-sm border-none">
          {length(@room.users)}/{@room.max_users}
        </div>

        <CoreComponents.button
          :if={@edit_path}
          navigate={@edit_path}
          class="btn-square bg-warning text-warning-content surface-elevated-sm surface-elevated-interactive ml-2"
        >
          <CoreComponents.icon name="hero-pencil-square" class="size-6" />
        </CoreComponents.button>
      </section>
    </section>
    """
  end

  attr :user, :map, required: true
  attr :slot_index, :integer, required: true
  attr :is_card_owner, :boolean, default: false
  attr :current_user_is_host, :boolean, default: false

  def user_card(assigns) do
    assigns = assign(assigns, :color_classes, get_user_color(assigns.slot_index))

    ~H"""
    <section
      name="user-card-indicator"
      class="indicator w-full group select-none"
      oncontextmenu={
        if @current_user_is_host and not @is_card_owner,
          do: "event.preventDefault(); document.getElementById('dropdown-btn-#{@user.id}')?.focus();",
          else: ""
      }
    >
      <span :if={@is_card_owner} class="indicator-item indicator-top indicator-end mt-2 mr-2 z-20">
        <CoreComponents.icon
          name="hero-star-solid"
          class="size-8 text-secondary drop-shadow-brutal"
        />
      </span>

      <section
        name="user-card-body"
        class={[
          "card w-full aspect-square rounded-none surface-elevated overflow-visible",
          @color_classes
        ]}
      >
        <div class="card-body p-3 items-center justify-between">
          <section
            :if={@current_user_is_host and not @is_card_owner}
            name="host-actions-dropdown"
            class="dropdown dropdown-bottom dropdown-right absolute top-2 left-2 z-50"
          >
            <div
              id={"dropdown-btn-#{@user.id}"}
              tabindex="0"
              role="button"
              class="btn btn-xs btn-square bg-base-100 text-base-content surface-elevated-sm md:opacity-0 md:group-hover:opacity-100 transition-opacity"
            >
              <CoreComponents.icon name="hero-ellipsis-vertical" class="size-4" />
            </div>

            <ul
              tabindex="0"
              class="dropdown-content menu bg-base-100 text-base-content rounded-none border-brutal shadow-brutal z-[100] w-36 p-0 mt-1"
            >
              <li>
                <button
                  type="button"
                  phx-click="transfer_owner"
                  phx-value-new_owner_id={@user.id}
                  class="rounded-none border-b-brutal hover:bg-warning hover:text-warning-content pixel-text text-xs p-3"
                >
                  <CoreComponents.icon name="hero-arrows-right-left" class="size-4" /> Transfer
                </button>
              </li>
              <li>
                <button
                  type="button"
                  phx-click="kick_user"
                  phx-value-target_user_id={@user.id}
                  class="rounded-none hover:bg-error hover:text-error-content pixel-text text-xs p-3"
                >
                  <CoreComponents.icon name="hero-x-mark" class="size-4" /> Kick
                </button>
              </li>
            </ul>
          </section>

          <section
            name="user-avatar"
            class="flex-1 flex items-center justify-center hover:scale-110 transition-transform cursor-default"
          >
            <div class="mask mask-hexagon bg-base-100/30 w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center drop-shadow-brutal-sm">
              <span class="text-5xl sm:text-6xl drop-shadow-none">👾</span>
            </div>
          </section>

          <section
            name="user-nickname"
            class="badge badge-lg w-full bg-black text-white text-xs pixel-text border-brutal rounded-none truncate z-10 py-3"
          >
            {@user.nickname}
          </section>
        </div>
      </section>
    </section>
    """
  end

  defp get_user_color(index) do
    base_colors = [
      "bg-red-500 text-white",
      "bg-blue-600 text-white",
      "bg-green-500 text-white",
      "bg-pink-500 text-white",
      "bg-orange-500 text-black",
      "bg-yellow-400 text-black",
      "bg-purple-600 text-white",
      "bg-cyan-400 text-black",
      "bg-lime-400 text-black",
      "bg-amber-700 text-white"
    ]

    total_colors = length(base_colors)
    color_class = Enum.at(base_colors, rem(index, total_colors))
    cycle = div(index, total_colors)

    if cycle == 0, do: color_class, else: "#{color_class} brightness-[#{100 - cycle * 15}%]"
  end

  def empty_slot(assigns) do
    ~H"""
    <section
      name="empty-slot"
      class="card w-full aspect-square bg-base-300 rounded-none border-4 border-dashed border-base-content opacity-50"
    >
      <div class="card-body items-center justify-center p-0">
        <CoreComponents.icon name="hero-plus" class="size-10 text-base-content" />
      </div>
    </section>
    """
  end

  attr :room, :map, required: true
  attr :current_scope, :map, required: true
  attr :invite_link, :string, required: true

  def control_panel(assigns) do
    ~H"""
    <section
      name="control-panel"
      class="fixed bottom-0 left-0 w-full bg-base-200 p-4 z-50 border-t-brutal shadow-brutal-top"
    >
      <div class="max-w-7xl mx-auto flex gap-4">
        <CoreComponents.button
          :if={@current_scope.user.id == @room.user_id}
          phx-click={JS.dispatch("phx:copy", to: "#invite-link")}
          id="invite-link"
          data-clipboard-text={@invite_link}
          class="w-1/3 md:w-auto btn-primary surface-elevated surface-elevated-interactive pixel-text text-xs md:text-sm h-14"
        >
          <CoreComponents.icon name="hero-link" class="size-5 md:mr-2" />
          <span class="hidden md:inline">Copy Link</span>
        </CoreComponents.button>

        <CoreComponents.button
          :if={@current_scope.user.id == @room.user_id}
          phx-click="start_match"
          disabled={length(@room.users) < 2}
          class={[
            "flex-1 md:w-auto text-lg md:text-xl h-14 pixel-text surface-elevated",
            length(@room.users) < 2 && "bg-neutral text-neutral-content opacity-50 cursor-not-allowed",
            length(@room.users) >= 2 && "bg-accent text-accent-content surface-elevated-interactive"
          ]}
        >
          <CoreComponents.icon name="hero-play-solid" class="mr-2 size-6" />
          {if length(@room.users) < 2, do: "Waiting...", else: "Start Game!"}
        </CoreComponents.button>
      </div>
    </section>
    """
  end
end
