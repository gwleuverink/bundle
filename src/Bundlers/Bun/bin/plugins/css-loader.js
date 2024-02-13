const [css, sass, fs] = await Promise.all([
    import("lightningcss-wasm"),
    import("sass"),
    import("fs")
]);

const defaultOptions = {
  targets: [],
};

export default function (options = {}) {
  const opts = { ...defaultOptions, ...options };

  return {
    name: "Bundle CSS loader",
    async setup(build) {

      // Build plain css
      build.onLoad({ filter: /\.css$/ }, (args) => {

        const contents = fs.readFileSync(args.path, "utf8");

        return compile(contents, args.path, {
          targets: opts.targets,
        });
      });

      // Build sass
      build.onLoad({ filter: /\.scss$/ }, (args) => {

        if(!sass) {
            throw `BUNDLING ERROR: You need to install sass in order to support sass loading`
        }

        const result = sass.compile(args.path);

        return compile(result.css, args.path, {
          targets: opts.targets,
        });
      });
    },
  };
}

async function compile(content, path, options = {}) {

    if(!css) {
      throw `BUNDLING ERROR: You need to install lightning CSS in order to support CSS loading`
    }

    const imports = [];
    const targets = options.targets?.length
      ? css.browserslistToTargets(options.targets)
      : undefined;

    const { code, exports } = css.transform({
      filename: path,
      code: Buffer.from(content),
      cssModules: Boolean(options.cssModules),
      minify: true,
      targets,
      visitor: {
        Rule: {
          import(rule) {
            imports.push(rule.value.url);
            return [];
          },
        },
      },
    });


    if (imports.length === 0) {
      return {
        contents: `export default ${JSON.stringify(code.toString())};`,
        loader: "js",
      };
    }

    const imported = imports
      .map((url, i) => `import _css${i} from "${url}";`)
      .join("\n");
    const exported = imports.map((_, i) => `_css${i}`).join(" + ");

    return {
      contents: `${imported}\nexport default ${exported} + ${JSON.stringify(
        code.toString()
      )};`,
      loader: "js",
    };
  }
