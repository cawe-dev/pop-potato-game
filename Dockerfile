FROM elixir:1.19.5 AS base

# Phoenix live reload dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    git \
    inotify-tools \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Gobal Hex e Rebar
RUN mix local.hex --force && \
    mix local.rebar --force

# Development Image
FROM base AS development

ARG USER_ID=1000
ARG GROUP_ID=1000

# Users
RUN groupadd -g ${GROUP_ID} elixir && \
    useradd -u ${USER_ID} -g ${GROUP_ID} -m -s /bin/bash elixir

RUN chown -R elixir:elixir /app

USER elixir

RUN mix local.hex --force && \
    mix local.rebar --force

CMD ["bash", "-c", "mix deps.get && mix phx.server"]

# Builder Image
FROM base AS builder

ENV MIX_ENV=prod

COPY mix.exs mix.lock ./
RUN mix deps.get --only prod

COPY config config
COPY lib lib
COPY priv priv
COPY assets assets

RUN mix deps.compile && mix compile

RUN mix assets.deploy

RUN mix release

# Production Image
FROM debian:bookworm-slim AS production

RUN apt-get update && apt-get install -y \
    libstdc++6 \
    openssl \
    ncurses-base \
    bash \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=builder /app/_build/prod/rel/pop_potato_game ./

ENV HOME=/app \
    MIX_ENV=prod \
    PORT=4000

EXPOSE 4000

CMD ["bin/pop_potato_game", "start"]