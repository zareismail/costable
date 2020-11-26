Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'costable',
      path: '/costable',
      component: require('./components/Tool'),
    },
  ])
})
