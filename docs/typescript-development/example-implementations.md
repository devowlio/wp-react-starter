# Example implementations

The TypeScript client-side comes with some example implementations so you can better understand how things work.

All the example implementations contain a server-side (PHP) implementation, too:

{% page-ref page="../php-development/example-implementations.md" %}

## Menu page

There is a PHP example implementation of a [menu page](../php-development/example-implementations.md#menu-page), but it needs more coding not only on server-side. The entrypoint [`src/public/ts/admin.ts`](using-entrypoints.md) comes into game. It detects if the page is visible and automatically renders content with ReactJS. It includes - as mentioned previously - some notices and a todo list connected to a MobX store. We recommend to have a look at that files directly before we copy & paste all the stuff here again:

-   📁 `ts`
    -   📁 `components/*`
    -   📁 `models/*`
    -   📁 `store/*`
    -   📁 `style`
        -   📄 `admin.scss`
    -   📁 `utils/*`
    -   📄 `admin.tsx`

## Widget

There is a PHP example implementation of a [widget](../php-development/example-implementations.md#widget). A widget always needs a "visible part" so TypeScript together with ReactJS can do the job. We recommend to have a look at that files directly before we copy & paste all the stuff here again:

-   📁 `ts`
    -   📁 `style`
        -   📄 `widget.scss`
    -   📁 `widget/*`
    -   📄 `widget.tsx`

{% hint style="info" %}
You must determine if you need server-side rendered HTML output or ReactJS. It has something to do with SEO. If SEO is important to your plugin, it is recommend to use server-side rendering. For example, if you want to create a dashboard only for logged-in users you can surely use ReactJS.
{% endhint %}

## REST endpoint

There is a PHP example implementation of a [Hello World endpoint](../php-development/example-implementations.md#rest-endpoint). In this case it is important to "type" that endpoint with TypeScript interfaces:

`ts/wp-api/hello-world.get.tsx`: Describes the `GET` **request**, **parameters** and **response**:

```typescript
import {
    RouteLocationInterface,
    RouteHttpVerb,
    RouteResponseInterface,
    RouteRequestInterface,
    RouteParamsInterface
} from "@wp-reactjs-multi-starter/utils";

export const locationRestHelloGet: RouteLocationInterface = {
    path: "/hello",
    method: RouteHttpVerb.GET
};

export type RequestRouteHelloGet = RouteRequestInterface;

export type ParamsRouteHelloGet = RouteParamsInterface;

export interface ResponseRouteHelloGet extends RouteResponseInterface {
    hello: string;
}
```

To request the endpoint you can simply do this by:

```typescript
const result = await request<RequestRouteHelloGet, ParamsRouteHelloGet, ResponseRouteHelloGet>({
    location: locationRestHelloGet
});
```

The resulting object will be of type `ResponseRouteHelloGet` and you can easily access `result.hello`.
