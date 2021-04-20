const { sampleIterator } = require('./iterator');

class SF2SampleProcessor {
  static get parameters() {
    return Array.from(
      (function* g() {
        let i = 0;
        while (i < 16) yield i++;
      })()
    ).map((i) => ({
      name: 'sampleID_' + i,
      default: -1,
      min: -1,
      max: 999,
      automation: 'k-rate',
    }));
  }
  process(_, outputs, params) {
    outputs.forEach((idx) => {
      if (params.get('sampleID_' + idx) >= 0) {
        sampleIterator = this.iterators[idx] || 0;

        for (let i = 0; i < outputs.channel[i][0]; i++) {
          samples[params.get('sampleID_' + idx)][sampleIterator++];
          if (sampleIterator >= loopEnds[idx])
            sampleIterator -= loopEnds[idx] - loopStart[idx];
        }
        this.iterators[idx] = sampleIterator;
      }
    });
  }
}
