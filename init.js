/* global __, App, Feeds, PluginHost, Plugins, require, xhr */

Plugins.Af_Notifications = {
	self: this,

	canNotify: (notification) => {
		switch (notification.type) {
			case 'js_api':
				return Notification.permission === 'granted';
		}
	},

	checkNotifications: () => {
		Plugins.Af_Notifications.getNotifications((notifications) => {
			if (notifications.length) {
				Plugins.Af_Notifications.showNotifications(notifications);
			}
		});
	},

	getNotifications: (cb) => {
		xhr.json('backend.php', App.getPhArgs('af_notifications', 'get_notifications'), (reply) => {
			// console.dir('Plugins.Af_Notifications reply', reply);
			cb((reply && Array.isArray(reply.notifications)) ? reply.notifications : []);
		});
	},

	showNotifications: (notifications) => {
		notifications.forEach((n, index) => {
			if (!Plugins.Af_Notifications.canNotify(n)) return;

			switch (n.type) {
				case 'js_api': {
					window.setTimeout(() => {
						const body = __('{0}: "{1}" matched a filter!')
							.replace('{0}', n.feed_title)
							.replace('{1}', n.article_title);

						const notification = new Notification(__('[tt-rss] Article Notification'), {
							body,
							icon: 'images/favicon-72px.png',
							tag: n.article_guid_hashed,
						});

						notification.addEventListener('click', () => { Feeds.open({feed: n.feed_id}) });
					}, 200*index);
					break;
				}
			}
		});
	},
};

require(['dojo/_base/kernel', 'dojo/ready'], (dojo, ready) => {
	ready(() => {
		PluginHost.register(PluginHost.HOOK_INIT_COMPLETE, () => {
			if (!App.getInitParam('bw_limit')) {
				window.setTimeout(() => {
					Plugins.Af_Notifications.checkNotifications();
				}, 5E3);

				window.setInterval(() => {
					Plugins.Af_Notifications.checkNotifications();
				}, 60E3);
			}
		});
	});
});
