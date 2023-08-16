FROM alpine as intermediate
LABEL stage=intermediate
RUN apk update && \
    apk add --update git
RUN git clone --depth 1 --branch master https://github.com/racai-ai/RELATE.git
#RUN rm -fr /RELATE/src/components/{corola,downloads,eurovoc,lrlt,papers,robin,rown,teprolin,translate}

FROM php:7.2-apache
RUN apt-get update && apt-get install -y cron zip unzip python3 python3-venv parallel locales locales-all
COPY --from=intermediate /RELATE/src/ /site/
COPY src/ /site/
RUN chown -R www-data:www-data /site/
#COPY conf/000-default.conf /etc/apache2/sites-enabled/
COPY conf/apache2.conf /etc/apache2/

RUN python3 -m venv /venv

COPY docker/entrypoint.sh /
RUN chmod a+rx /entrypoint.sh
COPY docker/tasks.cron /etc/cron.d/
RUN chmod 0644 /etc/cron.d/tasks.cron
RUN crontab /etc/cron.d/tasks.cron
RUN touch /var/log/cron.log

ENV LC_ALL en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US.UTF-8

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/entrypoint.sh"]
