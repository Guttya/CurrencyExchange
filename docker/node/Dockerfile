FROM node:18
WORKDIR /app
COPY package.json yarn.lock ./
RUN yarn install --dev
COPY . .
CMD ["yarn", "start"]
