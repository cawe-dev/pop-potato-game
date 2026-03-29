defmodule PopPotatoGame.Application do
  # See https://hexdocs.pm/elixir/Application.html
  # for more information on OTP Applications
  @moduledoc false

  use Application

  @impl true
  def start(_type, _args) do
    children = [
      PopPotatoGameWeb.Telemetry,
      PopPotatoGame.Repo,
      {DNSCluster, query: Application.get_env(:pop_potato_game, :dns_cluster_query) || :ignore},
      {Phoenix.PubSub, name: PopPotatoGame.PubSub},
      # Start a worker by calling: PopPotatoGame.Worker.start_link(arg)
      # {PopPotatoGame.Worker, arg},
      # Start to serve requests, typically the last entry
      PopPotatoGameWeb.Endpoint
    ]

    # See https://hexdocs.pm/elixir/Supervisor.html
    # for other strategies and supported options
    opts = [strategy: :one_for_one, name: PopPotatoGame.Supervisor]
    Supervisor.start_link(children, opts)
  end

  # Tell Phoenix to update the endpoint configuration
  # whenever the application is updated.
  @impl true
  def config_change(changed, _new, removed) do
    PopPotatoGameWeb.Endpoint.config_change(changed, removed)
    :ok
  end
end
