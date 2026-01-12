FROM node:lts AS builder

ENV FORCE_COLOR=0

RUN apt install -y git

RUN corepack enable

WORKDIR /src

COPY . .

RUN npm ci

RUN npm run build

EXPOSE 80