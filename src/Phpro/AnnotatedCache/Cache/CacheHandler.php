<?php
declare(strict_types=1);

namespace Phpro\AnnotatedCache\Cache;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use Phpro\AnnotatedCache\Annotation\CacheAnnotationInterface;
use Phpro\AnnotatedCache\Collection\AnnotationCollection;
use Phpro\AnnotatedCache\Collector\ResultCollectorInterface;
use Phpro\AnnotatedCache\Interception\InterceptionInterface;
use Phpro\AnnotatedCache\Interception\InterceptionPrefixInterface;
use Phpro\AnnotatedCache\Interception\InterceptionSuffixInterface;
use Phpro\AnnotatedCache\Interceptor\InterceptorInterface;
use Phpro\AnnotatedCache\Interceptor\Result\ContentAwareResultInterface;

/**
 * Class CacheHandler
 *
 * @package Phpro\AnnotatedCache\CacheAnnotation
 */
class CacheHandler implements CacheHandlerInterface
{
    /**
     * @var InterceptorInterface[]
     */
    private $interceptors;

    /**
     * @var ResultCollectorInterface
     */
    private $resultCollector;

    /**
     * Interceptor constructor.
     *
     * @param ResultCollectorInterface $resultCollector
     */
    public function __construct(ResultCollectorInterface $resultCollector)
    {
        $this->interceptors = new ArrayCollection();
        $this->resultCollector = $resultCollector;
    }

    /**
     * @param InterceptorInterface $interceptor
     */
    public function addInterceptor(InterceptorInterface $interceptor)
    {
        $this->interceptors->add($interceptor);
    }

    /**
     * @param AnnotationCollection        $annotations
     * @param InterceptionPrefixInterface $interception
     *
     * @return mixed
     */
    public function interceptProxyPrefix(AnnotationCollection $annotations, InterceptionPrefixInterface $interception)
    {
        return $this->runInterceptors($annotations, $interception, function (
            InterceptorInterface $interceptor,
            CacheAnnotationInterface $annotation,
            InterceptionPrefixInterface $interception
        ) {
            return $interceptor->interceptPrefix($annotation, $interception);
        });
    }

    /**
     * @param AnnotationCollection        $annotations
     * @param InterceptionSuffixInterface $interception
     *
     * @return mixed
     */
    public function interceptProxySuffix(AnnotationCollection $annotations, InterceptionSuffixInterface $interception)
    {
        return $this->runInterceptors($annotations, $interception, function (
            InterceptorInterface $interceptor,
            CacheAnnotationInterface $annotation,
            InterceptionSuffixInterface $interception
        ) {
            return $interceptor->interceptSuffix($annotation, $interception);
        });
    }

    /**
     * @param AnnotationCollection  $annotations
     * @param InterceptionInterface $interception
     * @param Closure               $callback
     *
     * @return mixed
     */
    private function runInterceptors(
        AnnotationCollection $annotations,
        InterceptionInterface $interception,
        Closure $callback
    ) {
        foreach ($annotations as $annotation) {
            foreach ($this->interceptors as $interceptor) {
                if (!$interceptor->canInterceptAnnotation($annotation)) {
                    continue;
                }

                $result = $callback($interceptor, $annotation, $interception);
                $this->resultCollector->collect($result);

                if ($result instanceof ContentAwareResultInterface && $result->hasContent()) {
                    return $result->getContent();
                }
            }
        }

        return null;
    }
}
