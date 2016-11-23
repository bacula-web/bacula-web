server '82.194.77.25',
  user: 'baculaweb',
  roles: %w{app},
  ssh_options: {
    user: 'baculaweb', #overrides user setting above
    port: 27,
    keys: %w(/home/victor/.ssh/id_rsa),
    forward_agent: false,
    auth_methods: %w(publickey password)
}