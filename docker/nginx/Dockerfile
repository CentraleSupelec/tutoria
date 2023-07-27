FROM nginx:1.19-alpine as nginx_dev

RUN apk add --no-cache openssl && \
 openssl req -x509 -nodes -days 365 -subj "/C=CA/ST=QC/O=CentraleSupélec, Inc./CN=tutorat-local.centralesupelec.fr" -addext "subjectAltName=DNS:tutorat-local.centralesupelec.fr" -newkey rsa:2048 -keyout /usr/share/new-selfsigned.key -out /usr/share/new-selfsigned.crt
COPY docker/nginx/default-dev.conf.template /etc/nginx/templates/default.conf.template
