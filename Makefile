DEPLOY_TO=fes-forum@palpatine.steven-mcdonald.id.au
DEPLOY_FROM=https://github.com/theflatearthsociety/forum.tfes.org.git
DEPLOY_BRANCH=master

all:
	# Intentionally a noop. There is nothing useful for 'make' to do
	# here.

deploy:
	set -e; \
	d=$$(date +%Y%m%d-%H%M%S); \
	ssh $(DEPLOY_TO) " \
		set -e; set -x; \
		git clone -b $(DEPLOY_BRANCH) $(DEPLOY_FROM) releases/$${d}; \
		ln -sfn releases/$${d} current; \
		sudo svc -t /etc/service/fastcgi.fes-forum; \
	"

.PHONY: all deploy
