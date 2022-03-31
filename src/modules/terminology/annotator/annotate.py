import argparse
import html
import re
import conllu


def read_terminology(dict_lemmas):
    dict_terms = {}

    with open(args.terminology_path, "r", encoding="utf-8") as csv_file:
        headers = csv_file.readline().strip().split("|")
        dict_headers = {header: idx for idx, header in enumerate(headers)}

        for line in csv_file:
            tokens = line.split("|")
            if tokens[dict_headers["L_CODE"]] == args.terminology_language:
                terminology = tokens[dict_headers["T_TERM"]].lower()
                terminology = html.unescape(terminology)
                terminology = terminology.replace("ţ", "ț").replace("ş", "ș")
                terminology = re.sub(r'<.*?>', "", terminology)

                terminology_lemma = " ".join([dict_lemmas.get(term, term) for term in terminology.split()])

                dict_terms[terminology_lemma] = tokens[dict_headers["E_ID"]]

    return dict_terms


def filter_word(word):
    word = word.lower()
    word = html.unescape(word)
    word = word.replace("ţ", "ț").replace("ş", "ș")

    return word


def read_lemmas():
    dict_lemmas = {}

    with open(args.lemma_path, "r", encoding="utf-8") as file:
        for line in file:
            if line[0] not in ["&", "#"]:
                tokens = line.split()
                word = filter_word(tokens[0])
                lemma = filter_word(tokens[1])

                if word not in dict_lemmas or (dict_lemmas[word][1] != "N" and tokens[2][0] == "N"):
                    dict_lemmas[word] = (lemma, tokens[2][0]) if lemma != "=" else (word, tokens[2][0])

    dict_lemmas = {k: v1 for k, (v1, v2) in dict_lemmas.items()}

    return dict_lemmas


def generate_strings(sentence):
    set_strings = set()
    list_strings = []

    for term_length in range(1, args.max_terminology_words):
        for i in range(len(sentence)):
            list_lemmas = []

            for j in range(0, term_length):
                if i + j < len(sentence):
                    list_lemmas.append(sentence[i + j]["lemma"].lower())
                    sent_string = " ".join(list_lemmas)

                    if (sent_string, i, i + j + 1) not in set_strings:
                        list_strings.append((sent_string, i, i + j + 1))
                        set_strings.add((sent_string, i, i + j + 1))

    return list_strings


def annotate_files(dict_terms):
    with open(args.data_path, "r", encoding="utf-8") as in_conllu_file:
        sentences = conllu.parse(in_conllu_file.read())

    with open(args.output_path, "w", encoding="utf-8") as out_conllu_file:
        for sentence in sentences:
            if "global.columns" in sentence.metadata and args.column_name not in sentence.metadata["global.columns"]:
                sentence.metadata["global.columns"] += " " + args.column_name

            for token in sentence:
                token[args.column_name.lower()] = "_"

            list_sent_strings = generate_strings(sentence)

            term_counter = 1
            for sent_string, start, end in list_sent_strings:
                # print(sent_string, start, end)

                if sent_string in dict_terms:
                    # print("----> ", sent_string, start, end)
                    for token_idx in range(start, end):
                        if sentence[token_idx][args.column_name.lower()] == "_":
                            sentence[token_idx][args.column_name.lower()] = "{}:{}".format(
                                term_counter, dict_terms[sent_string]
                            )
                        else:
                            sentence[token_idx][args.column_name.lower()] += ",{}:{}".format(
                                term_counter, dict_terms[sent_string]
                            )

                    term_counter += 1

            out_conllu_file.write(sentence.serialize())


def main():
    dict_lemmas = read_lemmas()
    dict_terms = read_terminology(dict_lemmas)
    annotate_files(dict_terms)


if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("data_path")
    parser.add_argument("output_path")
    parser.add_argument("--terminology_path", default="examples/terminology.csv")
    parser.add_argument("--lemma_path", default="examples/tbl.wordform.ro")
    parser.add_argument("--column_name", default="CURLICAT:IATE")
    parser.add_argument("--max_terminology_words", type=int, default=10)
    parser.add_argument("--terminology_language", default="ro")

    args = parser.parse_args()

    main()