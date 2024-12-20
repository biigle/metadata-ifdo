<?php
namespace Biigle\Tests\Modules\MetadataIfdo;

use Biigle\MediaType;
use Biigle\Modules\MetadataIfdo\IfdoParser;
use Symfony\Component\HttpFoundation\File\File;
use Biigle\Shape;
use TestCase;

class IfdoParserTest extends TestCase
{
    public function testRecognizesFileImage()
    {
        $file   = new File(__DIR__ . "/files/image-ifdo.json");
        $parser = new IfdoParser($file);
        $this->assertTrue($parser->recognizesFile());
    }

    public function testRecognizesFileVideo()
    {
        $file   = new File(__DIR__ . "/files/video-example-1.json");
        $parser = new IfdoParser($file);
        $this->assertTrue($parser->recognizesFile());
    }

    public function testGetMetadata()
    {
        $file   = new File(__DIR__ . "/files/image-ifdo.json");
        $parser = new IfdoParser($file);
        $data   = $parser->getMetadata();
        $this->assertSame(MediaType::imageId(), $data->type->id);
        $this->assertSame('SO268 SO268-2_100-1_OFOS SO_CAM-1_Photo_OFOS', $data->name);
        $this->assertNull($data->url);
        $this->assertSame('20.500.12085/d7546c4b-307f-4d42-8554-33236c577450', $data->handle);
        $this->assertCount(2, $data->getFiles());
        $file = $data->getFiles()->last();
        $this->assertSame('SO268-2_100-1_OFOS_SO_CAM-1_20190406_052726.JPG', $file->name);
        $this->assertSame('2019-04-06 05:27:26.000000', $file->takenAt);
        $this->assertSame(-117.0214286, $file->lng);
        $this->assertSame(11.8582192, $file->lat);
        $this->assertSame(-4129.6, $file->gpsAltitude);
        $this->assertSame(2.1, $file->distanceToGround);
        $this->assertSame(5.1, $file->area);
        $this->assertSame(21.0, $file->yaw);

        $this->assertCount(7, $file->getAnnotations());
        $annotation = array_pop($file->annotations);

        $this->assertSame(Shape::polygonId(), $annotation->shape->id);
        $this->assertSame('Hans Wurst', $annotation->labels[0]->user->name);
        $this->assertSame('4b6f42ff-6198-4b52-aa1c-fde5aa50265b', $annotation->labels[0]->user->uuid);
        $this->assertSame('Trash', $annotation->labels[0]->label->name);
        $this->assertSame('8a45f7e9-86aa-4ca8-bd58-2b2178ec4163', $annotation->labels[0]->label->uuid);
        $this->assertSame('ff5900', $annotation->labels[0]->label->color);
        $this->assertSame(4715.16, $annotation->points[0]);
    }

    public function testGetVideoMetadata()
    {
        $file   = new File(__DIR__ . "/files/video-example-1.json");
        $parser = new IfdoParser($file);
        $data   = $parser->getMetadata();
        $this->assertSame(MediaType::videoId(), $data->type->id);
        $this->assertSame('SO268 SO268-2_100-1_OFOS SO_CAM-1_Photo_OFOS', $data->name);
        $this->assertNull($data->url);
        $this->assertSame('20.500.12085/d7546c4b-307f-4d42-8554-33236c577450', $data->handle);
        $this->assertCount(1, $data->getFiles());
        $file = $data->getFiles()->last();
        $this->assertSame('SO242_2_163-1_LowerHD.mp4', $file->name);
        $this->assertSame('2019-04-06 04:29:27.000000', $file->takenAt);
        $this->assertSame(-117.0214286, $file->lng);
        $this->assertSame(11.8582192, $file->lat);
        $this->assertSame(-4129.6, $file->gpsAltitude);
        $this->assertSame(2.1, $file->distanceToGround);
        $this->assertSame(5.1, $file->area);
        $this->assertSame(21.0, $file->yaw);

        $this->assertCount(1, $file->getAnnotations());
        $annotation = array_pop($file->annotations);

        $this->assertSame(Shape::circleId(), $annotation->shape->id);
        $this->assertSame('Timm Schoening', $annotation->labels[0]->user->name);
        $this->assertSame('4b6f42ff-6198-4b52-aa1c-fde5aa50265b', $annotation->labels[0]->user->uuid);
        $this->assertSame('Animal', $annotation->labels[0]->label->name);
        $this->assertSame('8a45f7e9-86aa-4ca8-bd58-2b2178ec4163', $annotation->labels[0]->label->uuid);
        $this->assertSame('ff5900', $annotation->labels[0]->label->color);
        $this->assertSame(681.7, $annotation->points[0][0]);
        $this->assertSame(0.402947, $annotation->frames[0]);
    }

    public function testGetMetadataNoLabels()
    {
        $file   = new File(__DIR__ . "/files/image-ifdo-no-annotations.json");
        $parser = new IfdoParser($file);
        $data   = $parser->getMetadata();
        $this->assertSame(MediaType::imageId(), $data->type->id);
        $this->assertSame('SO268 SO268-2_100-1_OFOS SO_CAM-1_Photo_OFOS', $data->name);
    }
}
