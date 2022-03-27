# jsonresume-gemini

`jsonresume-gemini` is a single purpose server implementation of the Gemini protocol:  server up your JSON Resume over the Gemini protocol.

The [Gemini protocol](https://gemini.circumlunar.space/) is a new internet protocol which much lighter then the web.  Harks back to a simpler time of the early web, Gemini is a much easier to implement protocol, and `text/gemini` is bare-bones easy.  Early web and gopher nostalgia.

[JSON Resume](https://jsonresume.org/) is a JSON schema for describing one's resume.  There are numerous solutions out there for converting `jsonresume` to `html` or `pdf`.  This is my take on `jsonresume` applied to the world of Gemini space.

`jsonresume-gemini` does it's business as follows:

* A [ReactPHP](https://reactphp.org/) implementation of the Gemini protocol
* Server side middleware that maps Gemini requests to sections of your `jsonresume` for dynamic generation
* [Laminas](https://getlaminas.org/) based templating system 

## Stand up your own `jsonresume-gemini` server

* Install `php-cli` (v8+)
* `git clone` this repo
* Create your own SSL certificate
  * `openssl req -x509 -nodes -newkey rsa:4096 -keyout key.pem -out cert.pem -days 365`
  * `cat key.pem >> cert.pem`
* Update `config.ini` with the path to your `jsonresume`
* 🔥 Fire it up `php server.php`

Optionally, on a `systemd` based OS, you can setup `jsonresume-gemini` to run as a service.

* Create the file `/etc/systemd/system/jsonresume-gemini.service`
```text
[Unit]
Description=JSON Resume Gemini Server

[Service]
User=gemini
Type=simple
TimeoutSec=0
WorkingDirectory=/srv/jsonresume-gemini/
PIDFile=/var/run/jsonresume-gemini.pid
ExecStart=/usr/bin/php -f /srv/jsonresume-gemini/server.php
KillMode=process

Restart=on-failure
RestartSec=42s

[Install]
WantedBy=default.target
```
* `systemctl enable jsonresume-gemini`
* `systemctl start jsonresume-gemini`