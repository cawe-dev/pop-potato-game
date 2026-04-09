defmodule PopPotatoGameWeb.ThemeLive.Index do
  use PopPotatoGameWeb, :live_view

  alias PopPotatoGame.Admin

  @impl true
  def render(assigns) do
    ~H"""
    <Layouts.app flash={@flash}>
      <.header>
        Listing Themes
        <:actions>
          <.button variant="primary" navigate={~p"/themes/new"}>
            <.icon name="hero-plus" /> New Theme
          </.button>
        </:actions>
      </.header>

      <.table
        id="themes"
        rows={@streams.themes}
        row_click={fn {_id, theme} -> JS.navigate(~p"/themes/#{theme}") end}
      >
        <:col :let={{_id, theme}} label="Name">{theme.name}</:col>
        <:col :let={{_id, theme}} label="Icon">{theme.icon}</:col>
        <:action :let={{_id, theme}}>
          <div class="sr-only">
            <.link navigate={~p"/themes/#{theme}"}>Show</.link>
          </div>
          <.link navigate={~p"/themes/#{theme}/edit"}>Edit</.link>
        </:action>
        <:action :let={{id, theme}}>
          <.link
            phx-click={JS.push("delete", value: %{id: theme.id}) |> hide("##{id}")}
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
     |> assign(:page_title, "Listing Themes")
     |> stream(:themes, list_themes())}
  end

  @impl true
  def handle_event("delete", %{"id" => id}, socket) do
    theme = Admin.get_theme!(id)
    {:ok, _} = Admin.delete_theme(theme)

    {:noreply, stream_delete(socket, :themes, theme)}
  end

  defp list_themes() do
    Admin.list_themes()
  end
end
