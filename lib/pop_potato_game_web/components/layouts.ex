defmodule PopPotatoGameWeb.Layouts do
  use PopPotatoGameWeb, :html

  embed_templates "layouts/*"

  attr :flash, :map, required: true, doc: "the map of flash messages"
  attr :current_scope, :map, default: nil, doc: "the current scope"
  attr :page_title, :string, default: nil, doc: "the title of the current page for breadcrumbs"

  slot :inner_block, required: true

  def app(assigns) do
    ~H"""
    <header
      name="main-header"
      class="navbar bg-primary border-b-brutal px-4 sm:px-8"
    >
      <section name="navbar-logo" class="navbar-start">
        <.link
          href={~p"/"}
          class="md:text-xl font-black text-primary-content uppercase tracking-widest hover:scale-105"
        >
          🥔 POP POTATO
        </.link>
      </section>

      <section name="navbar-actions" class="navbar-end flex gap-2">
        <.theme_toggle />

        <section name="desktop-menu" class="hidden md:flex items-center gap-3">
          <%= if @current_scope do %>
            <div class="badge text-[10px] py-3 surface-elevated-sm">
              {@current_scope.user.nickname}
            </div>

            <.link
              href={~p"/users/settings"}
              class="btn btn-sm btn-square bg-info text-info-content border-brutal surface-elevated-sm hover:translate-y-px hover:shadow-none"
              title="Settings"
            >
              <.icon name="hero-cog-6-tooth" class="size-5" />
            </.link>

            <.link
              href={~p"/users/log-out"}
              method="delete"
              class="btn btn-sm bg-error text-error-content border-brutal surface-elevated-sm text-[10px] hover:translate-y-px hover:shadow-none"
            >
              Log out
            </.link>
          <% else %>
            <.link
              href={~p"/users/register"}
              class="btn btn-sm bg-secondary text-secondary-content border-brutal surface-elevated-sm text-[10px] hover:translate-y-px hover:shadow-none"
            >
              Register
            </.link>
            <.link
              href={~p"/users/log-in"}
              class="btn btn-sm bg-warning text-warning-content border-brutal surface-elevated-sm text-[10px] hover:translate-y-px hover:shadow-none"
            >
              Log in
            </.link>
          <% end %>
        </section>

        <section name="mobile-menu-dropdown" class="dropdown dropdown-end md:hidden">
          <div
            tabindex="0"
            role="button"
            class="btn btn-sm btn-square border-brutal surface-elevated-sm"
          >
            <.icon name="hero-bars-3" class="size-5" />
          </div>
          <ul
            tabindex="0"
            class="dropdown-content menu bg-base-100 text-base-content border-brutal shadow-brutal mt-3 w-52 p-2"
          >
            <%= if @current_scope do %>
              <li class="menu-title text-[8px] text-base-content opacity-70">
                {@current_scope.user.nickname}
              </li>
              <li>
                <.link href={~p"/users/settings"} class=" text-xs ">
                  <.icon name="hero-cog-6-tooth" class="size-4" /> Settings
                </.link>
              </li>
              <li>
                <.link
                  href={~p"/users/log-out"}
                  method="delete"
                  class=" text-xs text-error hover:bg-error hover:text-error-content "
                >
                  <.icon name="hero-arrow-right-on-rectangle" class="size-4" /> Log out
                </.link>
              </li>
            <% else %>
              <li>
                <.link href={~p"/users/register"} class=" text-xs ">
                  Register
                </.link>
              </li>
              <li>
                <.link href={~p"/users/log-in"} class=" text-xs ">Log in</.link>
              </li>
            <% end %>
          </ul>
        </section>
      </section>
    </header>

    <main
      name="main-content"
      class="w-full max-w-7xl mx-auto pt-4 md:pt-6 px-4 sm:px-6 lg:px-8 flex flex-col gap-4"
    >
      <section
        name="breadcrumbs"
        class="breadcrumbs text-xs sm:text-sm font-bold bg-base-200 border-brutal px-4 py-2 surface-elevated-sm w-fit max-w-full overflow-hidden"
      >
        <ul>
          <li>
            <.link
              href={~p"/"}
              class="inline-flex gap-2 items-center hover:text-primary transition-colors"
            >
              <.icon name="hero-home" class="size-4" /> Home
            </.link>
          </li>
          <%= if assigns[:page_title] do %>
            <li>
              <span class="inline-flex gap-2 items-center text-base-content/70">
                {assigns[:page_title]}
              </span>
            </li>
          <% end %>
        </ul>
      </section>

      {render_slot(@inner_block)}
    </main>

    <.flash_group flash={@flash} />
    """
  end

  @doc """
  Shows the flash group with standard titles and content.
  """
  attr :flash, :map, required: true, doc: "the map of flash messages"
  attr :id, :string, default: "flash-group", doc: "the optional id of flash container"

  def flash_group(assigns) do
    ~H"""
    <div id={@id} aria-live="polite">
      <.flash kind={:info} flash={@flash} />
      <.flash kind={:error} flash={@flash} />

      <.flash
        id="client-error"
        kind={:error}
        title={gettext("We can't find the internet")}
        phx-disconnected={show(".phx-client-error #client-error") |> JS.remove_attribute("hidden")}
        phx-connected={hide("#client-error") |> JS.set_attribute({"hidden", ""})}
        hidden
      >
        {gettext("Attempting to reconnect")}
        <.icon name="hero-arrow-path" class="ml-1 size-3 motion-safe:animate-spin" />
      </.flash>

      <.flash
        id="server-error"
        kind={:error}
        title={gettext("Something went wrong!")}
        phx-disconnected={show(".phx-server-error #server-error") |> JS.remove_attribute("hidden")}
        phx-connected={hide("#server-error") |> JS.set_attribute({"hidden", ""})}
        hidden
      >
        {gettext("Attempting to reconnect")}
        <.icon name="hero-arrow-path" class="ml-1 size-3 motion-safe:animate-spin" />
      </.flash>
    </div>
    """
  end

  @doc """
  DaisyUI Swap Theme Controller.
  """
  def theme_toggle(assigns) do
    ~H"""
    <label class="swap swap-rotate btn btn-sm btn-square bg-base-200 border-brutal  surface-elevated-sm hover:translate-y-px hover:shadow-none">
      <input
        type="checkbox"
        class="theme-controller"
        value="dark"
        onchange="localStorage.setItem('phx:theme', this.checked ? 'dark' : 'light')"
      />

      <.icon name="hero-sun-solid" class="swap-off size-5" />

      <.icon name="hero-moon-solid" class="swap-on size-5" />
    </label>

    <script>
      window.addEventListener("DOMContentLoaded", () => {
        const theme = localStorage.getItem("phx:theme");
        const toggle = document.querySelector(".theme-controller");
        if(theme === "dark" && toggle) toggle.checked = true;
      });
    </script>
    """
  end
end
