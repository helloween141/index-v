FROM laravelsail/php81-composer
ARG uid
RUN useradd -u $uid -ms /bin/bash index