module.exports = {
  url: "/api/v1/goals/categories",
  requestMethod: "GET",
  needLogin:true,
  cases: [
    {
      name: "Get all",
      expectStatus:200,
      expectJSONTypes: {
        path: 'data.*',
        json: {
            id: Number,
            name: String,
        },
      },
      expectJSONLength: {
        path: 'data.*',
        length: 3
      }
    },
    {
      name: "Get 1",
      requestParameter:{
        id: 1
      },
      expectStatus:200,
      expectJSONTypes: {
        path: 'data',
        json: {
            id: 1,
            name: "test",
        },
      }
    },
    {
      name: "Get 2",
      requestParameter:{
        id: 2
      },
      expectStatus:200,
      expectJSONTypes: {
        json: {
          data: {
            id: Number,
            name: String,
          }
        },
      }
    },

  ]
}
