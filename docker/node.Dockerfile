FROM node:26-alpine

# The base image bundles npm 11; pin the newer major.
RUN npm install -g npm@12.2.2
