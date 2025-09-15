FROM registry.cn-hangzhou.aliyuncs.com/jcleng/library-alpine:3.20.1
# php: https://github.com/crazywhalecc/static-php-cli
RUN wget https://github.com/jcleng/filearchive/releases/download/202509070510_php-8.4.11-cli-linux-x86_64.tar.gz/php-8.4.11-cli-linux-x86_64.tar.gz -O /tmp/php-8.4.11-cli-linux-x86_64.tar.gz \
  && tar xvf /tmp/php-8.4.11-cli-linux-x86_64.tar.gz -C /usr/bin/ \
  && rm -rf /tmp/php-8.4.11-cli-linux-x86_64.tar.gz
# composer
COPY --from=registry.cn-hangzhou.aliyuncs.com/jcleng/library-composer:latest /usr/bin/composer /usr/bin/composer
#
RUN mkdir -p /src
WORKDIR /src
COPY . /src
ENV SERVER_HOST=localhost
ENV SERVER_PORT=25565
EXPOSE 80
RUN composer install --ignore-platform-reqs && rm -rf ~/.composer/cache

CMD ["php", "-S", "0.0.0.0:80"]
