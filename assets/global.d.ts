interface Window {
      Routing: {
        generate: (route: string, parameters?: Record<string, any>) => string;
      };
      locale: string,
}
