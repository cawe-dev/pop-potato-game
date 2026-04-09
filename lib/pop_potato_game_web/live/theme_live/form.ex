defmodule PopPotatoGameWeb.ThemeLive.Form do
  use PopPotatoGameWeb, :live_view

  alias PopPotatoGame.Admin
  alias PopPotatoGame.Admin.Theme

  @impl true
  def render(assigns) do
    ~H"""
    <Layouts.app flash={@flash}>
      <.header>
        {@page_title}
        <:subtitle>Use this form to manage theme records in your database.</:subtitle>
      </.header>

      <.form for={@form} id="theme-form" phx-change="validate" phx-submit="save">
        <.input field={@form[:name]} type="text" label="Name" />
        <.input field={@form[:icon]} type="text" label="Icon" />
        <footer>
          <.button phx-disable-with="Saving..." variant="primary">Save Theme</.button>
          <.button navigate={return_path(@return_to, @theme)}>Cancel</.button>
        </footer>
      </.form>
    </Layouts.app>
    """
  end

  @impl true
  def mount(params, _session, socket) do
    {:ok,
     socket
     |> assign(:return_to, return_to(params["return_to"]))
     |> apply_action(socket.assigns.live_action, params)}
  end

  defp return_to("show"), do: "show"
  defp return_to(_), do: "index"

  defp apply_action(socket, :edit, %{"id" => id}) do
    theme = Admin.get_theme!(id)

    socket
    |> assign(:page_title, "Edit Theme")
    |> assign(:theme, theme)
    |> assign(:form, to_form(Admin.change_theme(theme)))
  end

  defp apply_action(socket, :new, _params) do
    theme = %Theme{}

    socket
    |> assign(:page_title, "New Theme")
    |> assign(:theme, theme)
    |> assign(:form, to_form(Admin.change_theme(theme)))
  end

  @impl true
  def handle_event("validate", %{"theme" => theme_params}, socket) do
    changeset = Admin.change_theme(socket.assigns.theme, theme_params)
    {:noreply, assign(socket, form: to_form(changeset, action: :validate))}
  end

  def handle_event("save", %{"theme" => theme_params}, socket) do
    save_theme(socket, socket.assigns.live_action, theme_params)
  end

  defp save_theme(socket, :edit, theme_params) do
    case Admin.update_theme(socket.assigns.theme, theme_params) do
      {:ok, theme} ->
        {:noreply,
         socket
         |> put_flash(:info, "Theme updated successfully")
         |> push_navigate(to: return_path(socket.assigns.return_to, theme))}

      {:error, %Ecto.Changeset{} = changeset} ->
        {:noreply, assign(socket, form: to_form(changeset))}
    end
  end

  defp save_theme(socket, :new, theme_params) do
    case Admin.create_theme(theme_params) do
      {:ok, theme} ->
        {:noreply,
         socket
         |> put_flash(:info, "Theme created successfully")
         |> push_navigate(to: return_path(socket.assigns.return_to, theme))}

      {:error, %Ecto.Changeset{} = changeset} ->
        {:noreply, assign(socket, form: to_form(changeset))}
    end
  end

  defp return_path("index", _theme), do: ~p"/themes"
  defp return_path("show", theme), do: ~p"/themes/#{theme}"
end
