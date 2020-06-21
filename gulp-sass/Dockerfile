FROM node:12-alpine

WORKDIR /site

RUN \
  apk add --no-cache dumb-init && \
  npm update && \
  npm install --global gulp-cli && \
  npm install gulp gulp-sass gulp-cached gulp-dependents gulp-sourcemaps gulp-debug && \
  chown -R node:node /site

ENTRYPOINT [ "/usr/bin/dumb-init", "--" ]
CMD [ "gulp" ]
