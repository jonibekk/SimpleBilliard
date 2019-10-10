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
    private $originRecords = [];
    /** @var \Aws\S3\S3Client */
    private $s3Instance;

    /**
     * S3Reader constructor.
     * @param string $bucket
     * @param string $path
     */
    public function __construct(string $bucket, string $path)
    {
        $this->bucket = $bucket;
        $this->path = $path;
        $this->s3Instance = AwsClientFactory::createS3ClientForFileStorage();
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
        $headerColumnCount = count($header);

        $records = [];
        foreach ($this->getOriginRecords() as $index => $record) {
            $data = [];
            if ($record === [null]) {
                continue;
            }

            if ($headerColumnCount > 0 && $headerColumnCount !== count($record)) {
                throw new \RuntimeException('The number of CSV items is incorrect. (line ' . ($index + 2). ')');
            }

            foreach ($record as $index => $value) {
                if (empty($header)) {
                    $data[] = $value;
                } else {
                    $key = $header[$index];
                    $data[$key] = $value;
                }
            }
            $records[] = $data;
        }

        return $records;
    }

    /**
     * @return void
     */
    protected function initialize(): void
    {
        if (!$this->doesBucketExist()) {
            throw new \RuntimeException('Bucket not exist.');
        }

        $response = $this->getS3Object();
        $this->responseValidate($response);

        if (!$response['Body']) {
            throw new \RuntimeException('The body information of the response does not exist.');
        }

        $data = [];
        $rowNumber = 0;

        $stream = $response['Body'];
        $stream->rewind();
        // FIXME: Convert character encoding more strictly
        $contents = mb_convert_encoding($stream->getContents(), 'UTF-8', 'sjis-win');
        $handle = tmpfile();
        fwrite($handle, $contents);
        rewind($handle);

        while (($lineData = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if ($rowNumber === 1) {
                continue;
            }
            $data[] = $lineData;
        }

        fclose($handle);

        $this->originRecords = $data;
    }

    /**
     * @return array
     */
    protected function getOriginRecords(): array
    {
        return $this->originRecords;
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
        return $this->s3Instance->doesBucketExist($this->bucket, $this->path);
    }

    /**
     * @return \Aws\Result
     */
    private function getS3Object(): \Aws\Result
    {
        return $this->s3Instance->getObject([
            'Bucket' => $this->bucket,
            'Key'    => $this->path,
        ]);
    }

    /**
     * @param \Aws\Result $response
     */
    private function responseValidate(\Aws\Result $response): void
    {
        $contentType = $response['@metadata']['headers']['content-type'] ?? '';
        if ($contentType !== 'text/csv') {
            throw new \RuntimeException('It is not a csv format file.');
        }
    }
}
