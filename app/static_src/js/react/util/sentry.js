export function sentrySend(e) {
  if (cake.sentry_dsn && cake.env_name !== 'local') {
    Raven.captureException(e);
  }
}
