defmodule PopPotatoGameWeb.PageController do
  use PopPotatoGameWeb, :controller

  def home(conn, _params) do
    render(conn, :home)
  end
end
