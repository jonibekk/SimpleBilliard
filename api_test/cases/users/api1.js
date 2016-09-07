module.exports = {
  url: "/api/v1/users",
  requestMethod: "POST",
  requestHeader: {},
  cases: [
    {
      name: "SAMPLE TEST",
      requestParameter:{
        id: 1,
        test:"aaa"
      },
      expectStatus:200,
      expectJSON:[
        {
          "id": 1,
          "name": "yamada",
          "gender": 1
        },
        {
          "id": 2,
          "name": "satou",
          "gender": 2
        },
        {
          "id": 3,
          "name": "tanaka",
          "gender": 1
        },
        {
          "id": 4,
          "name": "yoshida",
          "gender": 2
        }
      ],
    },
    {
      name: "SAMPLE TEST",
      requestParameter:{
        id: 1
      },
      expectStatus:400,
      expectJSON:{"test":"tet"},
    },

  ]
}
