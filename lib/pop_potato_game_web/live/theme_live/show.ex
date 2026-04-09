defmodule PopPotatoGameWeb.ThemeLive.Show do
  use PopPotatoGameWeb, :live_view

  alias PopPotatoGame.Admin

  @impl true
  def render(assigns) do
    ~H"""
    <Layouts.app flash={@flash}>
      <.header>
        Theme {@theme.id}
        <:subtitle>This is a theme record from your database.</:subtitle>
        <:actions>
          <.button navigate={~p"/themes"}>
            <.icon name="hero-arrow-left" />
          </.button>
          <.button variant="primary" navigate={~p"/themes/#{@theme}/edit?return_to=show"}>
            <.icon name="hero-pencil-square" /> Edit theme
          </.button>
        </:actions>
      </.header>

      <.list>
        <:item title="Name">{@theme.name}</:item>
        <:item title="Icon">{@theme.icon}</:item>
      </.list>
    </Layouts.app>
    """
  end

  @impl true
  def mount(%{"id" => id}, _session, socket) do
    {:ok,
     socket
     |> assign(:page_title, "Show Theme")
     |> assign(:theme, Admin.get_theme!(id))}
  end
end
