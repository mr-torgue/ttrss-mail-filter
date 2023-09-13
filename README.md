# ttrss-mail-filter
Adds a send mail notification action in the filter menu.
This allows user to get mail nofitications for every article matching a filter.

# Prerequisites
Must have [mailer-smtp](https://git.tt-rss.org/fox/ttrss-mailer-smtp.git/) installed.
User must have configured its own email address under preferences->personal data.

## Mailer SMTP Installation for Docker
Can be a bit tricky when running from docker.
I just installed postfix on the host system and let it listen to the docker0 interface.
Installation of postfix for outgoing mails only can be found on [Digital Ocean](https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-postfix-as-a-send-only-smtp-server-on-ubuntu-22-04).
After that add look up the docker0 address and bridge
1. Lookup bridge id for ttrss: "docker network ls | grep ttrss"
2. Use this output to find the ip of this network: "ip addr | grep [bridge id]"
3. Lookup docker0 ip addrss: "ip addr | grep docker0"
4. Add the following to /etc/postfix/main.cf:
```ini
mynetworks = 127.0.0.0/8 [docker0 ip]/16 [bridge ip]/16
inet_interfaces = 127.0.0.1, [docker0 interface ip] 
```
Just run "service postfix restart" and you should be good to go.

For the installation of mailer-smtp, simply do:
1. Find plugin.local directory using "docker volume inspect ttrss-docker_app | grep Mountpoint"
2. Clone git repository: "git clone https://git.tt-rss.org/fox/ttrss-mailer-smtp.git mailer_smtp"
3. Change .env file (in your docker compose directory) to:
```ini
TTRSS_PLUGINS=auth_internal,mailer_smtp
TTRSS_SMTP_SERVER=[docker0 interface ip]:25
TTRSS_SMTP_LOGIN=
TTRSS_SMTP_PASSWORD=
#TTRSS_SMTP_SECURE=tls
```

# Installation and usage
Use "git clone https://github.com/mr-torgue/ttrss-mail-filter mail_filter" to clone the code into your plugin.local directory (can be found with "docker volume inspect ttrss-docker_app | grep Mountpoint").
In ttrss, go to preferences->Prefences->Plugins and enable the mail_filter plugin.

To use it go to preferences->Filters->Create filter. Under Apply actions, click Add and select Invoke plugin. Select the Mail_Filter action.
In the Match tab, just create any filter you like. Give it a name and save. 
Congrats! Everytime this filter matches an article, you will get notified!

# Todo
Currently there are a few limitations:
1. No custom mail address: we can only send to the mail address of the currently logged in user
2. No filter name in the mail notification: so you don't know which filter triggered this
3. No from address
