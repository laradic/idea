<?php

namespace Laradic\Tests\Idea;

use App\User;
use Laradic\Idea\DocBlock\ClassDoc;
use Laradic\Idea\DocBlock\MethodDoc;
use Illuminate\Database\Eloquent\Model;
use Laradic\Idea\DocBlock\DocBlockGenerator;

class DocBlockTest extends TestCase
{
    public function testGenerator()
    {
        $generator = new DocBlockGenerator();
        $generator
            ->class(User::class)
            ->ensureTag('method', 'static User whereRole()')
            ->ensureTag('method', 'User[] whereRoles()');

        $processed = $generator->process();

        $this->assertIsArray($processed);
    }

    public function testClassDoc()
    {
        $doc     = new ClassDoc(User::class);
        $content = $doc->process();
        $doc->ensureTag('method', 'static User whereRole()');
        $doc->ensureTag('method', 'User[] whereRoles()');
        $newContent = $doc->process();
        $this->assertNotEquals($content, $newContent);
        return $newContent;
    }

    public function testMethodDoc()
    {
        $doc     = new MethodDoc(Model::class, '__toString');
        $content = $doc->process();
        $doc->ensureTag('param', 'int $val');
        $newContent = $doc->process();
        $this->assertNotEquals($content, $newContent);
        return $newContent;
    }
}
