# Bluemix based Custom Hook for Kinetise Backend 

[Kinetise](https://www.kinetise.com/) applications can communicate with any Backend or any API, specifically with Kinetise Backend.

Kinetise Backend allows adding custom logic to table endpoints by attaching `triggers` or `hooks` - depending on the configuration.

This is an example application that modifies default `description` column in `sample` table.

## How to start?

Deploy this solution to bluemix, follow the tutorial and you will get a working custom hook for Kinetise Backend.

[![Deploy to Bluemix](https://bluemix.net/deploy/button.png)](https://bluemix.net/deploy?repository=https://github.com/Kinetise/Bluemix-Kinetise-Sample-App.git)

This project creates runtime instance with PHP, creates a ClearDb service, binds them together. Initializes the database with seed data.

## How do Triggers and Hooks work?

![Triggers and Hooks](https://raw.githubusercontent.com/Kinetise/Bluemix-Kinetise-Sample-App/master/htdocs/img/triggers_and_hooks.png "Triggers and Hooks")

### Basic concept

Every time Kinetise CMS endpoint is called *cloud code* web-hook is invoked to either:

1. Alter how the data is formatted when fetched from / inserted into a table. Those web-hooks should return response which later alters Kinetise CMS endpoints' behavior. 
2. Notify about a data change event. Those web-hooks' response does not modify Kinetise CMS endpoint's behavior.

**Examples**: 

* Assume invitation e-mail should be send every time an invitation is created in Kinetise CMS. Second type of web-hook should be used, because it does not alter the way invitation is stored inside Kinetise CMS.
* Assume a tax has to be added to an order every time an order is placed. First type of web-hook should be used, because it has to modify the data that is going to be stored inside Kinetise CMS.

For the rest of this document the first type will be referred to as *hooks*, while the second one as *triggers*.

### Web-hook method description

#### Request

Both web-hook types should handle `POST` requests with following JSON body:

```
{
  "projectGuid": "${projectIdentifier}",
  "table": "${tableName}",
  "originalRequest": {
    "params": {
      "${parameterOneName}": "${parameterOneValue}",
      "${parameterTwoName}": "${parameterTwoValue}"
    },
    "headers": {
      "${headerOneName}": "${headerOneValue}",
      "${headerTwoName}": "${headerTwoValue}"
    },
    "body": {
      "${firstBodyPropertyName}": "${firstBodyPropertyValue}",
      "${secondBodyPropertyName}": "${secondBodyPropertyValue}"
    }
  },
  "data": {
    "columns": {
      "${firstColumnName}": "${firstColumnType}",
      "${secondColumnName}": "${secondColumnType}"
    },
    "rows": [
      {
        "${firstRowFirstColumnName}": "${firstRowFirstColumnValue}",
        "${firstRowSecondColumnName}": "${firstRowSecondColumnValue}"
      },
      {
        "${secondRowFirstColumnName}": "${secondRowFirstColumnValue}",
        "${secondRowSecondColumnName}": "${secondRowSecondColumnValue}"
      }
    ]
  }
}
```

where:

* `projectGuid` describes project identifier,
* `table` describes name of a table in behalf of which an endpoint was originally invoked,
* `originalRequest` describes properties of a request send to *Kinetise CMS*:
	* `parameters` is a dictionary of query parameters passed in request's URL,
	* `headers` is a dictionary of headers passed in request,
	* `body` is a copy of body passed to a request (present only for `POST` requests),
* `data` describes rows and columns of originally requested data:
	* `columns` contain pairs of column names and column types,
	* `rows` is an array with descriptions of every item connected to original request.

#### Response

##### Status codes

2xx HTTP success status code should be returned to indicate that web-hook completed successfully. Any 4xx or 5xx HTTP error status code will make original Kinetise CMS fail.

##### Response body

In case of triggers request body is not checked. Any hook should respond with body in following format:

```
{
  "columns": {
    "${firstColumnName}": "${firstColumnType}",
    "${secondColumnName}": "${secondColumnType}"
  },
  "rows": [
    {
      "${firstRowFirstColumnName}": "${firstRowFirstColumnValue}",
      "${firstRowSecondColumnName}": "${firstRowSecondColumnValue}"
    },
    {
      "${secondRowFirstColumnName}": "${secondRowFirstColumnValue}",
      "${secondRowSecondColumnName}": "${secondRowSecondColumnValue}"
    }
  ]
}
```

where:

* `columns` describes returned data scheme,
* `rows` describes actual returned data.

### Cloud Code types

#### Hooks

##### User

| No. | Endpoint | Hook type | Description |
|:----|---|-------|-----|----|---|
| 1. | `alterapi` | `pre_register` | It is invoked before user registration is completed with AlterAPI form. It receives details of a freshly registered user and expects to get formatted data of this user back. | 
| 2. | `facebook` | `pre_register` | It is invoked before first Facebook login for a user is completed. Works the same as the `pre_register` one for `alterapi`. |
| 3. | `linkedin` | `pre_register` | It is invoked before first LinkedIn login for a user is completed. Works the same as the `pre_register` one for `alterapi`. |
| 4. | `get-users` | `format_data` | It is invoked before data is converted to Kinetise format for `get-users` endpoint. It receives list of user details fetched from Kinetise CMS database and expects to get a formatted list of users back. |

##### Data table

| No. | Endpoint | Hook type | Description |
|:----|---|-------|-----|----|---|
| 1. | `get-table` | `format_data` | It is invoked before data is converted to Kinetise format for `get-table` endpoint. It receives list of table rows fetched from Kinetise CMS database and expects to get a formatted list of rows back. | 
| 2. | `create-row` | `pre_create` | It is invoked before data of a new entry is inserted into Kinetise CMS database in `create-row` endpoint. It receives list of table rows to insert and expects to get a formatted list of rows back. | 
| 3. | `update-row` | `pre_update` | It is invoked before updated data of existing entry is inserted into Kinetise CMS database in `update-row` endpoint. It receives list of table rows to insert and expects to get a formatted list of rows back.  | 

#### Triggers

##### User

| No. | Endpoint | Hook type | Description |
|:----|---|-------|-----|----|---|
| 1. | `alterapi` | `post_login` | It is invoked after user has logged in using AlterAPI login. It receives details of a logged in user. | 
| 2. | `alterapi` | `post_register` | It is invoked after user has registered using AlterAPI register endpoint. It receives details of a newly registered user. |
| 3. | `alterapi` | `post_logout` | It is invoked after user has logged out using AlterAPI logout endpoint. It receives details of a logged out user. |
| 4. | `facebook` | `post_login` | It is invoked after user has logged in using Facebook login. It receives details of a logged in user. | 
| 5. | `linkedin` | `post_login` | It is invoked after user has logged in using LinkedIn login. It receives details of a logged in user. | 

##### Data table

| No. | Endpoint | Hook type | Description |
|:----|---|-------|-----|----|---|
| 1. | `get-table` | `data_requested` | It is invoked after data is requested for `get-table` endpoint. It receives list of table rows that were returned to a user. | 
| 2. | `create-row` | `post_create` | It is invoked after data is inserted into Kinetise CMS database for `create-row` endpoint. It receives list of inserted table rows. | 
| 3. | `update-row` | `post_update` | It is invoked after data is updated in Kinetise CMS database for `update-row` endpoint. It receives list of updated table rows. | 
| 4. | `delete-row` | `post_delete` | It is invoked after data is deleted from Kinetise CMS database for `delete-row` endpoint. It receives list of removed table rows. | 

## How to work with this app?

This is a PHP application using Silex Framework.

The main code resides in `src\KinetiseSkeleton\Controller\Api\SampleController.php`:

    public function getAction(Request $request)
    {
        $json = $request->request->get('_json', array());
        $sampleDataRepository = $this->getEntityManager()->getRepository('KinetiseSkeleton\Doctrine\Entity\SampleData');
        $rows = $sampleDataRepository->findAll();
        $response['columns'] = $json['data']['columns'];
        $response['rows'] = $json['data']['rows'];
        foreach ($response['rows'] as $key => &$row) {
            if ( isset($rows[$key]) ) {
                /** @var SampleData $dbSampleDataObject */
                $dbSampleDataObject = $rows[$key];
                $row['description'] = $dbSampleDataObject->getDescription();
            } else {
                $row['description'] = "This row was modified by custom hook";
            }
        }
        return new JsonResponse($response);
    }