<?php
/**
 * Class Reader
 */
class S3Reader
{
    /** @var string */
    private $bucket;
    /** @var string */
    private $path;
    /** @var array */
    private $header = [];
    /** @var array */
    private $origin_records = [];
    /** @var \Aws\S3\S3Client */
    private $s3_instance;

    /**
     * S3Reader constructor.
     * @param string $bucket
     * @param string $path
     */
    public function __construct(string $bucket, string $path)
    {
        $this->bucket = $bucket;
        $this->path = $path;
        $this->s3_instance = AwsClientFactory::createS3ClientForFileStorage();
        $this->initialize();
    }

    /**
     * @param array $header
     * @return S3Reader
     */
    public function setHeader(array $header): self
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return array
     */
    public function getRecords(): array
    {
        $header = $this->getHeader();
        if (empty($header)) {
            return $this->getOriginRecords();
        }

        $column_count = count($header);

        $records = [];
        foreach ($this->getOriginRecords() as $index => $record) {
            $data = [];
            if ($record === [null]) {
                continue;
            }

            if ($column_count !== count($record)) {
                throw new\RuntimeException('There is not enough data needed. (line ' . ($index + 2). ')');
            }

            foreach ($record as $index => $value) {
                $key = $header[$index];
                $data[$key] = $value;
            }
            $records[] = $data;
        }

        return $records;
    }

    /**
     * @return array
     */
    private function getOriginRecords(): array
    {
        return $this->origin_records;
    }

    /**
     * @return array
     */
    private function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @return bool
     */
    private function doesBucketExist(): bool
    {
        return $this->s3_instance->doesBucketExist($this->bucket, $this->path);
    }

    /**
     * @return \Aws\Result
     */
    private function getS3Object(): \Aws\Result
    {
        $s3Instance = AwsClientFactory::createS3ClientForFileStorage();
        return $s3Instance->getObject([
            'Bucket' => $this->bucket,
            'Key'    => $this->path,
        ]);
    }

    /**
     * @param \Aws\Result $response
     */
    private function responseValidate(\Aws\Result $response): void
    {
        $content_type = $response['@metadata']['headers']['content-type'] ?? '';
        if ($content_type !== 'text/csv') {
            throw new \RuntimeException('It is not a csv format file.');
        }
    }

    /**
     * initialize
     * @return void
     */
    private function initialize(): void
    {
        if (!$this->doesBucketExist()) {
            throw new \RuntimeException('CSV file does not exist.');
        }

        $response = $this->getS3Object();
        $this->responseValidate($response);

        if (!$response['Body']) {
            throw new \RuntimeException('The body information of the response does not exist.');
        }

        $data = [];
        $row_number = 0;

        $stream = $response['Body'];
        $stream->rewind();
        $contents = mb_convert_encoding($stream->getContents(), 'UTF-8', 'sjis-win');
        $handle = tmpfile();
        fwrite($handle, $contents);
        rewind($handle);

        while (($line_data = fgetcsv($handle)) !== false) {
            $row_number++;
            if ($row_number === 1) {
                continue;
            }
            $data[] = $line_data;
        }

        fclose($handle);

        $this->origin_records = $data;
    }
}
