module.exports = {
  url: "/api/v1/goals/create",
  requestMethod: "POST",
  needLogin:true,
  cases: [
    {
      name: "Auth",
      expectStatus: 401,
      expectJSONTypes: {
        json: {
          message: "Failed authentication"
        },
      },
    },
    {
      name: "Validation no parameters",
      expectStatus: 400,
      expectJSONTypes: {
        path: 'validation_errors',
        json: {
          name: String,
          category: String,
        },
      },
    }
  ]
}
